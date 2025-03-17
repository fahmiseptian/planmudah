<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    protected $data = [];
    protected $Model = [];
    public function __construct()
    {
        $this->Model['User'] = new User();
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $this->data['member_id'] = $user->id;
            } else {
                // Jika tidak ada token, member_id dan member_type tidak diset atau diatur default
                $this->data['member_id'] = null;
            }
        } catch (JWTException $e) {
            // Jika token tidak valid atau tidak ditemukan, atur nilai default
            $this->data['member_id'] = null;
        }
    }
    public function detailUser($id)
    {
        $data = User::find($id);
        if (empty($data)) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan',
                'success' => false
            ]);
        }
        return response()->json([
            'data' => $data,
            'success' => true
        ]);
    }
    public function updateUser(Request $request)
    {
        $name = $request->name;
        $id = $this->data['member_id'];

        $update = User::where('id', $id)->update(['name' => $name]);
        if ($update) {
            return response()->json([
                'message' => 'Pengguna berhasil diupdate',
                'success' => true
            ]);
        }
        return response()->json([
            'message' => 'Gagal mengupdate pengguna',
            'success' => false
        ]);
    }
    public function changePassword(Request $request) {
        $newPassword = $request->passwordNew;
        $passwordOld = $request->passwordOld;
        $id = $this->data['member_id'];

        $passwordHash = $this->Model['User']->getPasswordHash($id);

        if (Hash::check($passwordOld, $passwordHash)) {
            $update = User::where('id', $id)->update(['password' => bcrypt($newPassword)]);
            if ($update) {
                return response()->json([
                    'message' => 'Password berhasil diupdate',
                    'success' => true
                ]);
            }
            return response()->json([
                'message' => 'Gagal mengupdate password',
                'success' => false
            ]);
        } else {
            return response()->json([
                'message' => 'Password lama tidak benar',
                'success' => false
            ]);
        }
    }
}
