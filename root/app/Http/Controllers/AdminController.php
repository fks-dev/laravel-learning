<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admins = Admin::all();
        $logins = AdminLog::orderbyDesc('id')->get();

        return view('admin.adminMgmt.index', compact('admins', 'logins'));
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
        return view('admin.adminMgmt.create');
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

        return redirect()->route('adminMgmt.index')->with('message', $request->username.'を登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        return view('admin.adminMgmt.edit', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin->update([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('adminMgmt.index')->with('message', $request->username.'の情報を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('adminMgmt.index')->with('danger', $admin->username.'を削除しました');
    }

    /**
     * CSVファイルダウンロード
     */
    public function csv()
    {
        $csvRecords = self::getAdminCsvRecords();
        return self::streamDownloadCsv('adminMgmt.csv', $csvRecords);
    }

    // レコード取得
    private static function getAdminCsvRecords():array
    {
        $admins = Admin::withTrashed()->get();
        $csvRecords = [
            ['ID', 'ユーザー名', 'パスワード', 'メールアドレス', '削除日時', '作成日時', '更新日時'],
        ];
        foreach ($admins as $admin) {
            $csvRecords[] = [
                $admin->id,
                $admin->username,
                $admin->password,
                $admin->mail_address,
                $admin->deleted_at,
                $admin->created_at,
                $admin->updated_at,
            ];
        }
        return $csvRecords;
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
        $contentType = 'text/plain';
        if ($separator === ',') {
            $contentType = 'text/csv';
        } elseif ($separator === "\t") {
            $contentType = 'text/tab-separated-values';
        }
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
     * インポート画面
     */
    public function import()
    {
        return view('admin.adminMgmt.import');
    }

    /**
     * CSVインポート
     */
    public function importStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required | mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $file = $request->file('csv_file');

        if (($handle = fopen($file, 'r')) !== false) {

            // ヘッダー部分の読み込み
            $header = fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $username = $data[0];
                $password = Hash::make($data[1]);
                $mail_address = $data[2];

                Admin::create([
                    'username' => $username,
                    'password' => $password,
                    'mail_address' => $mail_address,
                ]);
            }
            fclose($handle);
        }

        return redirect()->route('adminMgmt.index')->with('message', 'CSVファイルをインポートしました。');
    }
}
