<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Http;


use App\Models\AudioModel;

class UserController extends Controller
{
    // save to local
    // public function store(Request $request)
    // {
    //     // validation
    //     $validate = $this->validate($request, [
    //         'uploader' => 'numeric|required',
    //         'title' => 'required',
    //         'audio' =>'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac'
    //     ]); 
    //     if (!$validate) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'validate data error',
    //             'data' => $validate,
    //         ]);
    //     }

    //     // handle single file 
    //     try {
    //         DB::beginTransaction();  
    //         if($request->hasFile('audio')){
    //             $file =$request->file('audio');
    //             $uniqueid=uniqid();
    //             $original_name=$request->file('audio')->getClientOriginalName();
    //             $size=$request->file('audio')->getSize();
    //             $extension=$request->file('audio')->getClientOriginalExtension();
    //             $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
    //             $audiopath=url('/storage/upload/files/audio/'.$filename);
    //             $path=$file->storeAs('public/upload/files/audio/',$filename);
    //             $all_audios=$audiopath;

    //             $data = AudioModel::create([
    //                 "uploader" => $request->uploader,
    //                 "title" => $request->title,
    //                 "audio_url" =>  $all_audios,
    //             ]);
    //             DB::commit();
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'upload data successfull',
    //                 'data' => $data,
    //             ]);
    //         }

    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'failed to upload audio',
    //             'data' => $th
    //         ]);
    //     }
    // }

    public function store(Request $request)
    {
        // validation
        $validate = $this->validate($request, [
            'audio' =>'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac'
        ]); 

        if (!$validate) {
            return response()->json([
                'status' => false,
                'message' => 'audio cant be null',
                'data' => $validate,
            ]);
        }

        // handle single file 
        try {
            DB::beginTransaction();  
            if($request->hasFile('audio')){
                // preparation audio
                $uniqueid=uniqid();
                // $original_name=$request->file('audio')->getClientOriginalName();
                // $size=$request->file('audio')->getSize();
                $extension=$request->file('audio')->getClientOriginalExtension();
                $filename=Carbon::now()->format('Ymd').'_'.'ringkas'.'_'.$uniqueid.'.'.$extension;
                
                // preparation upload audio to google bucket
                $config = [
                    'keyFilePath' => base_path("public/storage/ringkas-370814-3e67bc7e26e6.json"),
                    'projectId' => env("GOOGLE_CLOUD_PROJECT_ID"),
                ];
                $bucketName = env("GOOGLE_CLOUD_STORAGE_BUCKET");
                $source =$request->file('audio');

                // upload to google bucket
                $storage = new StorageClient($config);
                $file = fopen($source, 'r');
                $bucket = $storage->bucket($bucketName);
                $object = $bucket->upload($file, [
                    'name' => $filename
                ]);

                // prepatarion audio name
                $audiopath= ' gs://' .  $bucketName .'/'. $filename; // convert to string like this gs://bucket-name/file-name.wav

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'upload data successfully',
                    'audio_url' => $audiopath,
                ]);
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to upload audio',
                'data' => $th
            ]);
        }
    }

    public function hitYou(Request $request){
        try {
            $url = 'http://54.255.13.136/ringkas/long-audio';
            $data = array('url' => $request->url);
            $options = array(
                    'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                )
            );
            
            $context  = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            // to remove "\"{\\\" in return data and convert to json
            $response=str_replace('},

            ]',"}

            ]",$response);

            return response()->json([
                'status' => true,
                'message' => 'transkip data successfully',
                'data' => json_decode($response, true),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'failed to transkip audio',
                'data' => $th
            ]);
        }
    }

    public function submit(Request $request)
    {
        // validation
        $validate = $this->validate($request, [
            'uploader' => 'numeric|required',
            'title' => 'required',
            'audio' =>'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'start_at' => 'required',
            'detail' => 'required',
            'full_text' => 'nullable',
            'most_occur' => 'nullable',
            'url_wordcloud' => 'nullable',
        ]); 

        if (!$validate) {
            return response()->json([
                'status' => false,
                'message' => 'validate data error',
                'data' => $validate,
            ]);
        }

        try {
            DB::beginTransaction(); 
            
            // upload to db
            $data = AudioModel::create([
                "uploader" => $request->uploader,
                "title" => $request->title,
                "audio_url" => $request->audio_url,
                "start_at" => $request->start_at,
                "detail" => $request->detail,
                "full_text" => $request->full_text,
                "most_occur" => $request->most_occur,
                "url_wordcloud" => $request->url_wordcloud, 
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'submit data successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to submit data',
                'data' => $th
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            // env
            $config = [
                'keyFilePath' => base_path("public/storage/ringkas-370814-3e67bc7e26e6.json"),
                'projectId' => env("GOOGLE_CLOUD_PROJECT_ID"),
            ];
            $bucketName = env("GOOGLE_CLOUD_STORAGE_BUCKET");

            $name = substr($request->fileName,20);
    
            // upload to google bucket
            $storage = new StorageClient($config);
            $bucket = $storage->bucket($bucketName);
            $object = $bucket->object($name);
            $object->delete();

            // return 
            return response()->json([
                'status' => true,
                'message' => 'upload data successfully',
                'name_file' => $name,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to delete audio',
                'data' => $th
            ]);
        }

    }
}
