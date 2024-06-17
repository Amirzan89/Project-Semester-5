<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\JwtController;
use App\Http\Controllers\Services\MailController;
use App\Models\User;
use App\Models\Verifikasi;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class UserController extends Controller
{
    public function getDefaultFoto(Request $request){
        $referrer = $request->headers->get('referer');
        if (!$referrer && $request->path() == 'public/download/foto') {
            abort(404);
        }
        return response()->download(storage_path('app/' . 'admin/default.jpg'), 'foto.' . pathinfo('admin/default.jpg', PATHINFO_EXTENSION));
    }
    public function getFotoProfile(Request $request){
        $userAuth = $request->input('user_auth');
        $referrer = $request->headers->get('referer');
        if (!$referrer && $request->path() == 'public/download/foto') {
            abort(404);
        }
        if (empty($userAuth['foto']) || is_null($userAuth['foto'])) {
            $defaultPhotoPath = 'admin/default.jpg';
            return response()->download(storage_path('app/' . $defaultPhotoPath), 'foto.' . pathinfo($defaultPhotoPath, PATHINFO_EXTENSION));
        } else {
            $filePath = storage_path('app/admin/foto/' . $userAuth['foto']);
            if (!empty($userAuth['foto'] && !is_null($userAuth['foto'])) && file_exists($filePath) && is_file($filePath)) {
                return response(Crypt::decrypt(file_get_contents($filePath)));
            }
            abort(404);
        }
    }
    public function getChangePass(Request $request, User $user, $any = null){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'code' =>'nullable'
        ],[
            'email.required'=>'Email harus di isi',
            'email.email'=>'Email yang anda masukkan invalid',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        if(Str::startsWith($request->path(), 'verify/password') && $request->isMethod('get')){
            $email = $request->query('email');
            if (!Verifikasi::whereRaw("BINARY link = ?", [$any])->exists()) {
                return Inertia::render('app', ['title' => 'Reset Password', 'message' => 'Link invalid', 'code' => 400, 'div' => 'red']);
            }
            if (!Verifikasi::whereRaw("BINARY email = ?", [$email])->exists()) {
                return Inertia::render('app', ['title' => 'Reset Password', 'message' => 'Email invalid', 'code' => 400, 'div' => 'red']);
            }
            if (!Verifikasi::whereRaw("BINARY email = ? AND BINARY link = ?", [$email, $any])->exists()) {
                return Inertia::render('app', ['title' => 'Reset Password', 'message' => 'Link invalid', 'code' => 400, 'div' => 'red']);
            }
            $currentDateTime = Carbon::now();
            if (!Verifikasi::whereRaw("BINARY email = ?", [$email])->where('updated_at', '>=', $currentDateTime->subMinutes(1))->exists()) {
                Verifikasi::whereRaw("BINARY email = ? AND deskripsi = 'password'", [$email])->delete();
                return Inertia::render('app', ['title' => 'Reset Password', 'message' => 'Link Expired', 'code' => 400, 'div' => 'red']);
            }
            return Inertia::render('app', ['email' => $email, 'title' => 'Reset Password', 'link' => $any, 'code' => '', 'div' => 'verifyDiv', 'deskripsi' => 'password']);
        }else{
            $email = $request->input('email');
            $code = $request->input('otp');
            if (!Verifikasi::whereRaw("BINARY email = ?", [$email])->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Email invalid'], 400);
            }
            if (!Verifikasi::whereRaw("BINARY email = ? AND BINARY kode_otp = ?", [$email, $code])->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Code OTP invalid'], 400);
            }
            $currentDateTime = Carbon::now();
            if (!DB::table('verifikasi')->whereRaw("BINARY email = ?", [$email])->where('updated_at', '>=', $currentDateTime->subMinutes(15))->exists()) {
                DB::table('verifikasi')->whereRaw("BINARY email = ? AND deskripsi = 'password'", [$email])->delete();
                return response()->json(['status' => 'error', 'message' => 'Code OTP expired'], 400);
            }
            return response()->json(['status' => 'success', 'message' => 'OTP Anda benar, silahkan ganti password']);
        }
    }
    public function changePassEmail(Request $request, User $user, JWTController $jwtController, RefreshToken $refreshToken){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'nama'=>'nullable',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:25',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'password_confirm' => [
                'required',
                'string',
                'min:8',
                'max:25',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'code' => 'nullable',
            'link' => 'nullable',
            'description'=>'required'
        ],[
            'email.required'=>'Email harus di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'password.required'=>'Password harus di isi',
            'password.min'=>'Password minimal 8 karakter',
            'password.max'=>'Password maksimal 25 karakter',
            'password.regex'=>'Password baru harus terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            'password_confirm.required'=>'Password konfirmasi konfirmasi harus di isi',
            'password_confirm.min'=>'Password konfirmasi minimal 8 karakter',
            'password_confirm.max'=>'Password konfirmasi maksimal 25 karakter',
            'password_confirm.regex'=>'Password konfirmasi terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            'description.required'=>'Deskripsi harus di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $email = $request->input('email');
        $pass = $request->input("password");
        $pass1 = $request->input("password_confirm");
        $code = $request->input('code');
        $link = $request->input('link');
        $desc = $request->input('description');
        if($pass !== $pass1){
            return response()->json(['status'=>'error','message'=>'Password Harus Sama'],400);
        }
        if(is_null($link) || empty($link)){
            if($desc == 'createUser'){
                $ins = User::insert([
                    'uuid' => Str::uuid(),
                    'email' => $request->input('email'),
                    'nama_lengkap' => $request->input('nama'),
                    'password' => Hash::make($request->input('password')),
                    'verifikasi' => true,
                    'role' => 'user',
                ]);
                if(!$ins){
                    return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
                }
                $data = $jwtController->createJWTWebsite($email,$refreshToken);
                if (!$data || $data['status'] == 'error') {
                    $errorMessage = $data ? $data['message'] : 'create token error';
                    return response()->json(['status' => 'error', 'message' => $errorMessage], 500);
                }
                return response()->json(['status'=>'success','message'=>'login sukses silahkan masuk dashboard'])
                ->cookie('token1',base64_encode(json_encode(['email'=>$email,'number'=>$data['number']])),time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED')),'/','',true)
                ->cookie('token2',$data['data']['token'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')),'/','',true)
                ->cookie('token3',$data['data']['refresh'],time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED')),'/','',true);
            }else{
                if (!Verifikasi::whereRaw("BINARY kode_otp = ?",[$code])->exists()) {
                    return response()->json(['status'=>'error','message'=>'Token invalid'], 400);
                }
                if (!User::whereRaw("BINARY email = ?",[$email])->exists()) {
                    return response()->json(['status'=>'error','message'=>'Invalid Email'], 400);
                }
                if (!Verifikasi::whereRaw("BINARY email = ? AND BINARY kode_otp = ?",[$email,$code])->exists()) {
                    return response()->json(['status'=>'error','message'=>'Invalid Email'], 400);
                }
                $currentDateTime = Carbon::now();
                if (!DB::table('verifikasi')->whereRaw("BINARY email = ?",[$email])->where('updated_at', '>=', $currentDateTime->subMinutes(15))->exists()) {
                    DB::table('verifikasi')->whereRaw("BINARY email = ? AND deskripsi = 'password'",[$email])->delete();
                    return response()->json(['status'=>'error','message'=>'Token expired'], 400);
                }
                if (is_null(DB::table('users')->whereRaw("BINARY email = ?",[$email])->update(['password'=>Hash::make($pass)]))) {
                    return response()->json(['status'=>'error','message'=>'Error updating password'], 500);
                }
                if (!DB::table('verifikasi')->whereRaw("BINARY email = ?",[$email])->delete()) {
                    return response()->json(['status'=>'error','message'=>'Error updating password'], 500);
                }
                return response()->json(['status'=>'success','message'=>'ganti password berhasil silahkan login']);
            }
        }else{
            if (!Verifikasi::whereRaw("BINARY link = ? AND deskripsi = ?", [$link, $desc])->exists()) {
                return response()->json(['status'=>'error', 'message'=>'Link expired'], 400);
            }
            if (!User::whereRaw("BINARY email = ?", [$email])->exists()) {
                return response()->json(['status'=>'error', 'message'=>'Invalid Email1'], 400);
            }
            if (!Verifikasi::whereRaw("BINARY email = ? AND BINARY link = ? AND deskripsi = ?", [$email, $link, $desc])->exists()) {
                return response()->json(['status'=>'error', 'message'=>'Email invalid'], 400);
            }
            $currentDateTime = Carbon::now();
            if (!DB::table('verifikasi')->whereRaw("BINARY email = ?", [$email])->where('updated_at', '>=', $currentDateTime->subMinutes(15))->exists()) {
                DB::table('verifikasi')->whereRaw("BINARY email = ? AND deskripsi = ?", [$email, $desc])->delete();
                return response()->json(['status'=>'error', 'message'=>'Link expired'], 400);
            }
            if (is_null(DB::table('users')->whereRaw("BINARY email = ?", [$email])->update(['password'=>Hash::make($pass)]))) {
                return response()->json(['status'=>'error', 'message'=>'Error updating password'], 500);
            }
            if (!DB::table('verifikasi')->whereRaw("BINARY email = ? AND deskripsi = ?", [$email, $desc])->delete()) {
                return response()->json(['status'=>'error', 'message'=>'Error updating password'], 500);
            }
            return response()->json(['status'=>'success', 'message'=>'ganti password berhasil silahkan login']);
        }
    }
    public function verifyEmail(Request $request, User $user, $any = null){
        $email = $request->query('email');
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'otp' =>'nullable'
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors = $errorMessages[0]; 
            }
            return response()->json(['status' => 'error', 'message' => $errors], 400);
        }
        $email = $request->input('email');
        $code = $request->input('otp');
        //check email on table user
        $user = User::select('nama_lengkap')->whereRaw("BINARY email = ?",[$request->input('email')])->first();
        if (is_null($user)) {
            return response()->json(['status'=>'error','message'=>'Email tidak terdaftar !'],400);
        }
        if(Str::startsWith($request->path(), 'verify/email') && $request->isMethod('get')){
            //check if user have create verify email
            $verify = Verifikasi::select('link','send','updated_at')->whereRaw("BINARY email = ?",[$request->input('email')])->where('deskripsi', 'email')->first();
            if (is_null($verify)) {
                return response()->json(['status'=>'error','message'=>'Email invalid'],400);
            }
            //check link
            if ($verify->link !== $any) {
                return response()->json(['status'=>'error','message'=>'link invalid'],400);
            }
            //check if mail not expired
            $expTime = MailController::getConditionOTP()[($verify->send - 1)];
            if (Carbon::parse($verify->updated_at)->diffInMinutes(Carbon::now()) >= $expTime) {
                return response()->json(['status'=>'error','message'=>'link expired'],400);
            }
            if(is_null(DB::table('users')->whereRaw("BINARY email = ?",[$email])->update(['verifikasi'=>true]))){
                return response()->json(['status'=>'error','message'=>'error verify email'],500);
            }
            $deleted = DB::table('verifikasi')->whereRaw("BINARY email = ?",[$email])->delete();
            if(!$deleted){
                return response()->json(['status'=>'error','message'=>'Error verifikasi Email'],500);
            }else{
                return response()->json(['status'=>'success','message'=>'verifikasi email berhasil silahkan login']);
            }
        }else{
            //check if user have create verify email
            $verify = Verifikasi::select('kode_otp','send','updated_at')->whereRaw("BINARY email = ?",[$request->input('email')])->where('deskripsi', 'email')->first();
            if (is_null($verify)) {
                return response()->json(['status'=>'error','message'=>'Email invalid'],400);
            }
            //check code
            if ($verify['kode_otp'] != $code) {
                return response()->json(['status'=>'error','message'=>'kode otp invalid'],400);
            }
            //check if mail not expired
            $expTime = MailController::getConditionOTP()[($verify->send - 1)];
            if (Carbon::parse($verify->updated_at)->diffInMinutes(Carbon::now()) >= $expTime) {
                return response()->json(['status'=>'error','message'=>'kode otp expired'],400);
            }
            // if(is_null(DB::table('users')->whereRaw("BINARY email = ?",[$email])->update(['verifikasi'=>true]))){
            //     return response()->json(['status'=>'error','message'=>'error verifikasi email'],500);
            // }
            $deleted = DB::table('verifikasi')->whereRaw("BINARY email = ?",[$email])->delete();
            if(!$deleted){
                return response()->json(['status'=>'error','message'=>'error verifikasi email'],500);
            }
            return response()->json(['status'=>'success','message'=>'verifikasi email berhasil silahkan login']);
        }
    }
    public function createUser(Request $request, MailController $mailController, Verifikasi $verify){
        $validator = Validator::make($request->only('nama','jenis_kelamin','no_telpon','tanggal_lahir','tempat_lahir','email','password','foto'), [
            'nama'=>'required',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'no_telpon' => 'nullable|digits_between:10,13',
            'email'=>'required | email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:25',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\p{P}\p{S}])[\p{L}\p{N}\p{P}\p{S}]+$/u',
            ],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ],[
            'nama.required'=>'Nama Lengkap wajib di isi',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'no_telpon.digits_between' => 'Nomor telepon tidak boleh lebih dari 13 karakter',
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'password.required'=>'Password wajib di isi',
            'password.min'=>'Password minimal 8 karakter',
            'password.max'=>'Password maksimal 25 karakter',
            'password.regex'=>'Password baru wajib terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Format foto tidak valid. Gunakan format jpeg, png, jpg',
            'foto.max' => 'Ukuran foto tidak boleh lebih dari 5MB',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        //process file foto
        // if ($request->hasFile('foto')) {
        //     $file = $request->file('foto');
        //     if(!($file->isValid() && in_array($file->extension(), ['jpeg', 'png', 'jpg']))){
        //         return response()->json(['status'=>'error','message'=>'Format Foto tidak valid. Gunakan format jpeg, png, jpg'], 400);
        //     }
        //     $destinationPath = storage_path('app/user/');
        //     $fotoName = $file->hashName();
        //     $fileData = Crypt::encrypt(file_get_contents($file));
        //     Storage::disk('user')->put('/' . $fotoName, $fileData);
        // }
        //create user
        $insert = User::insert([
            'uuid' =>  Str::uuid(),
            'nama_lengkap' => $request->input('nama'),
            'role' => 'user',
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'foto' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        if (!$insert) {
            return response()->json(['status'=>'error','message'=>'gagal register'], 400);
        }
        return $mailController->createVerifyEmail($request,$verify);
    }
    public function logout(Request $request, JWTController $jwtController){
        $userAuth = $request->input('user_auth');
        if (!RefreshToken::whereRaw("BINARY email = ?",[$userAuth['email']])->where('number',$userAuth['number'])->delete()) {
            return response()->json(['status' => 'error', 'message' => 'Gagal Logout'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Logout berhasil silahkan login kembali']);
    }
}