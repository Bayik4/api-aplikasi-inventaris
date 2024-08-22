<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // mengambil data user yang sedang login
    public function index()
    {
        return response()->json([
            "status" => true,
            "data" => Auth::user(),
            "message" => "Success get user"
        ]);
    }

    // update data user
    public function update(UserUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        // mengecek agar yang sedang user tidak bisa mengupdate data user lain jika dibuat multi user
        if (Auth::user()->id != $id) {
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [],
                "message" => "Your not allowed to update this data"
            ], 400));
        }

        // transaksi database
        DB::beginTransaction();
        try {
            User::where('id', $id)->update($data);
            DB::commit();
            return response()->json([
                "status" => true,
                "data" => [
                    "userId" => $id
                ],
                "message" => "Update data success"
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [],
                "errors" => $err,
                "message" => "Update data failed"
            ], 400));
        }
    }
}
