<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\StoreUserMgmtRequest;
use App\Http\Requests\UpdateUserMgmtRequest;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserMgmtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $logins = UserLogin::all();

        return view('admin.userMgmt.index', compact('users', 'logins'));
    }

    /**
     * 検索機能
     */
    public function search(Request $request)
    {
        $search = $request->input('name');

        $results = User::leftJoin('user_logs', 'users.id', '=', 'user_logs.user_id')
            ->where('users.username', 'LIKE', "%{$search}%")
            ->select('users.*', 'user_logs.updated_at as login_at')
            ->with('courses')
            ->get();

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();

        return view('admin.userMgmt.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserMgmtRequest $request)
    {
        User::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'mail_address' => $request->mail_address,
        ]);

        $user = User::orderByDesc('id')->first();

        $courses = $request->input('course', []);

        foreach ($courses as $courseId) {
            $course = Course::find($courseId);
            $user->Courses()->attach($course);
        }

        return redirect()->route('admin.userMgmt.index')->with('message', $request->username.'を登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $users = User::with('courses')->find($user);
        $courses = Course::all();
        return view('admin.userMgmt.edit', compact('user', 'users', 'courses'));
    }

    /**
     * パスワードの変更
     */
    public function password(User $user)
    {
        return view('admin.userMgmt.password', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserMgmtRequest $request, User $user)
    {
        $user->Courses()->detach();

        $user->update([
            'username'     => $request->username,
            'mail_address' => $request->mail_address,
        ]);

        $courses = $request->input('course', []);

        foreach ($courses as $courseId) {
            $course = Course::find($courseId);
            $user->Courses()->attach($course);
        }

        return redirect()->route('admin.userMgmt.index')->with('message', $request->username.'の情報を更新しました');
    }

    /**
     * パスワードの更新
     */
    public function changeUserPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), (new PasswordRequest())->rules());

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error_message', '現在のパスワードが正しくありません');
        }elseif ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.userMgmt.index')->with('message', 'パスワードが変更されました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.userMgmt.index')->with('danger', $user->username.'を削除しました');
    }

    /**
     * CSVファイルダウンロード
     */
    public function downloadCsv()
    {
        $fileName = 'user.csv';
        $csvRecords = self::getAdminCsvRecords();
        return self::streamDownloadCsv($fileName, $csvRecords);
    }

    // レコード取得
    private static function getAdminCsvRecords():array
    {
        $users = User::withTrashed()->get();
        $csvRecords = [
            ['ID', 'ユーザー名', 'メールアドレス', '削除日時', '作成日時', '更新日時'],
        ];
        foreach ($users as $user) {
            $csvRecords[] = [
                $user->id,
                $user->username,
                $user->mail_address,
                $user->deleted_at,
                $user->created_at,
                $user->updated_at,
            ];
        }
        return $csvRecords;
    }

    // CSV or TSV
    private static function determineContentType($separator)
    {
        if ($separator === ',') {
            'text/csv';
        } elseif ($separator === "\t") {
            'text/tab-separated-values';
        }
    }

    // CSVストリームダウンロード
    private static function streamDownloadCsv(
        string $name,
        iterable $fieldList,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = "\\",
        string $eol = "\r\n",
    ) {
        $contentType = self::determineContentType($separator);
        $headers = ['Content-Type' => $contentType];

        return response()->streamDownload(function () use ($fieldList, $separator, $enclosure, $escape, $eol) {
            $stream = fopen('php://output', 'w');
            foreach ($fieldList as $fields) {
                fputcsv($stream, $fields, $separator, $enclosure, $escape, $eol);
            }
            fclose($stream);
        }, $name, $headers);
    }

    /**
     * CSVインポート画面
     */
    public function createCsv()
    {
        return view('admin.userMgmt.import');
    }

    /**
     * CSVインポート
     */
    public function storeCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required | mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');

        if (!$handle) {
            return redirect()->route('adminMgmt.index')->with('danger', 'CSVファイルを開けませんでした。');
        }

        // ヘッダー部分の読み込み
        $length = 1000;
        $header = fgetcsv($handle, $length, ',');

        while (($data = fgetcsv($handle, $length, ',')) !== false) {
            $username = $data[1];
            $password = Hash::make('test');
            $mail_address = $data[2];

            User::create([
                'username' => $username,
                'password' => $password,
                'mail_address' => $mail_address,
            ]);
        }
        fclose($handle);
        return redirect()->route('admin.userMgmt.index')->with('message', 'CSVファイルをインポートしました。');
    }

}
