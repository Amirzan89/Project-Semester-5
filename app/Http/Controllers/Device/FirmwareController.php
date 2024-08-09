<?php
namespace App\Http\Controllers\Device;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Firmware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class FirmwareController extends Controller
{
    public function checkUpdate(Request $request){
        $validator = Validator::make($request->only('version'), [
            'version' => 'required|boolean',
        ],[
            'version.required' => 'Versi harus di isi',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $update = Firmware::where('version', '>', $request->input('current_version'))->first();
        // if ($update) {
        //     return response()->json(['update_version' => $update->version]);
        // } else {
        //     return response()->json(['update_version' => null]);
        // }
    }
    public function downloadUpdate(Request $request){
        $update = Firmware::where('version', '>', $request->input('current_version'))->first();
        if ($update) {
            return response()->json(['update_version' => $update->version]);
        } else {
            $firmware = Firmware::select('nama_event','poster_event')->where('id_event',$request->input('id_event'))->join('detail_events', 'events.id_detail', '=', 'detail_events.id_detail')->limit(1)->get()[0];
            if (!$firmware) {
                return response()->json(['status' => 'error', 'message' => 'Data Firmware tidak ditemukan'], 404);
            }
            $filePath = storage_path("app/event/{$firmware->poster_event}");
            if (!file_exists($filePath)) {
                return response()->json(['status' => 'error', 'message' =>'File Firmware tidak ditemukan'], 404);
            }
            $tempFilePath = tempnam(sys_get_temp_dir(), 'decrypted_file');
            file_put_contents($tempFilePath, Crypt::decrypt(Storage::disk('event')->get("poster_event/{$firmware->poster_event}")));
            return Response::download($tempFilePath, $firmware->nama_event. '.' .pathinfo($firmware->poster_event, PATHINFO_EXTENSION));
        }
    }
}