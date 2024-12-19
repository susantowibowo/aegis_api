<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\userEmail;
use Mail;

class UserController extends Controller
{
	protected $table = 'users';
	
    public function storeData(Request $request)
    {
		$email = $request->email;
		$password = $request->password;
		$name = $request->name;
		if(!$email || !$password || !$name){
			$data = [
				'status' => 400,
				'description' => 'Email, password dan name tidak boleh kosong',
			];
			return response()->json($data);
		}
		// cek email
		$arr_users = DB::table($this->table)->where("email","=",$email)->where("active","=","1")->first();
		if($arr_users){
			$data = [
				'status' => 400,
				'description' => 'Email '.$email.' sudah digunakan!',
			];
			return response()->json($data);
		}
		
		$tanggal_input = date("Y-m-d H:i:s");
        $data_save = [
			'email' => $email,
			'password' => $password,
			'name' => $name,
			'active' => 1,
			'created_at' => $tanggal_input,
		];
		$save_user = DB::table($this->table)->insertGetId(array_merge($data_save));
		if($save_user){
			// aktifkan untuk kirim ke email user
			/*
			if($email){
				$arr_newuser = DB::table($this->table)->where("id","=",$save_user)->first();
				$details = [
				  'id' => $arr_newuser->id,
				  'email' => $arr_newuser->email,
				  'password' => $arr_newuser->password,
				  'name' => $arr_newuser->name,
				  'created_at' => $arr_newuser->created_at,
				];
				Mail::to($email)->send(new userEmail($details));
			}
			*/

			$data = [
				'status' => 200,
				'description' => 'Data berhasil di simpan',
				'details' => [
					'id' => $save_user,
					'email' => $email,
					'name' => $name,
					'created_at' => $tanggal_input,
				]
			];
		} else {		
			$data = [
				'status' => 400,
				'description' => 'Data gagal di simpan!',
			];
		}
		return response()->json($data);
    }
    
	
	public function getData(Request $request)
	{
		$search = $request->search;
		$page = $request->page;
		$sortBy = $request->sortBy;
		// cek validasi field sortby
		$field_sort = array('created_at', 'name', 'email');
		if($sortBy){
			if(in_array($sortBy,$field_sort)){
				$sort_orders = strtolower($sortBy);
			} else { $sort_orders = 'created_at'; }
		} else {
			$sort_orders = 'created_at';
		}
		
		$user_data = DB::table($this->table)
		->select("id", "email", "name", "created_at")
		->where('active', '=', '1')
		->where(function($w) use($search, $request) {
			if (!empty($search)) {
				$w->where(DB::raw("CONCAT(name, ' ', email)"), 'LIKE', "%$search%");
			}
		})
		->orderBy($sort_orders,"DESC")
        ->paginate(10);
		
		$results = array();
		if (!empty($user_data)) {
            foreach ($user_data as $row) {
                $results[] = [
                    'id' => $row->id,
                    'email' => $row->email,
                    'name' => $row->name,
                    'created_at' => $row->created_at,
                    'orders_count' => DB::table("orders")->select('id')->where("user_id","=",$row->id)->count(),
                ];
			}
		}
		
		if(!$page){$pagination=1;} else {$pagination=$page;}
		
		$data = [
			'page' => $pagination,
			'users' => $results,
		];
		
		return response()->json($data);
	}
}
