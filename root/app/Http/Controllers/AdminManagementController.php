<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminPasswordRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\AdminLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $adminUser = Auth::user();
        $admins = Admin::all();
        $logins = AdminLogin::all();

        return view('admin.admin-management.index', compact('adminUser', 'admins', 'logins'));
    }

    /**
     * 検索機能
     */
    public function search(Request $request)
    {
        $search = $request->input('name');

        $results = Admin::leftJoin('admin_logs', 'admins.id', '=', 'admin_logs.admin_id')
            ->where('admins.username', 'LIKE', "%{$search}%")
            ->select('admins.*', 'admin_logs.updated_at as login_at')
            ->get();

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $adminUser = Auth::user();
        return view('admin.admin-management.create', compact('adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {

        Admin::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', $request->username . 'を登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $adminUser = Auth::user();
        return view('admin.admin-management.edit', compact('adminUser'));
    }

    /**
     * パスワードの変更
     */
    public function password(Admin $admin)
    {
        $adminUser = Auth::user();
        return view('admin.admin-management.password', compact('admin', 'adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin->update([
            'username'     => $request->username,
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', $request->username . 'の情報を更新しました');
    }

    /**
     * パスワードの更新
     */
    public function changeAdminPassword(AdminPasswordRequest $request, Admin $admin)
    {

        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error_message', '現在のパスワードが正しくありません');
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', 'パスワードが変更されました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admin-management.index')->with('danger', $admin->username . 'を削除しました');
    }

    /**
     * CSVファイルダウンロード
     */
    public function downloadCsv()
    {
        $fileName = 'admin.csv';
        $csvRecords = self::getAdminCsvRecords();
        return self::streamDownloadCsv($fileName, $csvRecords);
    }

    // レコード取得
    private static function getAdminCsvRecords(): array
    {
        $admins = Admin::withTrashed()->get();
        $csvRecords = [
            ['ID', 'ユーザー名', 'メールアドレス', '削除日時', '作成日時', '更新日時'],
        ];
        foreach ($admins as $admin) {
            $csvRecords[] = [
                $admin->id,
                $admin->username,
                $admin->mail_address,
                $admin->deleted_at,
                $admin->created_at,
                $admin->updated_at,
            ];
        }
        return $csvRecords;
    }

    // CSV or TSV
    private static function determineContentType(string $separator)
    {
        if ($separator === ',') {
            return 'text/csv';
        } elseif ($separator === "\t") {
            return 'text/tab-separated-values';
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
        $adminUser = Auth::user();
        return view('admin.admin-management.import', compact('adminUser'));
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
            return redirect()->route('admin-management.index')->with('danger', 'CSVファイルを開けませんでした。');
        }

        // ヘッダー部分の読み込み
        $length = 1000;
        $header = fgetcsv($handle, $length, ',');

        while (($data = fgetcsv($handle, $length, ',')) !== false) {
            $username = $data[1];
            $password = Hash::make('admin');
            $mail_address = $data[2];

            Admin::create([
                'username' => $username,
                'password' => $password,
                'mail_address' => $mail_address,
            ]);
        }
        fclose($handle);
        return redirect()->route('admin.admin-management.index')->with('message', 'CSVファイルをインポートしました。');
    }
}
