<?php   
namespace App\Http\Controllers;

    use App\Models\{
        User,
        membresia
    };
    use Maatwebsite\Excel\Facades\Excel;
    use App\Exports\{
        UsersClientExport
    };
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Illuminate\Support\Facades\DB;
    use App\Http\Traits\HelpersTrait;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
    use HelpersTrait;
    public function example(){
        return 10;
    }
    public function export_users_excel(Request $request){
        $date = \Carbon\Carbon::now();
        $dayWithHyphen = $date->format('d_m_Y');
        $paremetro=$request->all();
        return Excel::download(new UsersClientExport($paremetro), 'Users_clientes_report'.$dayWithHyphen.'.xlsx');
    }//end
    public function getRecovery(Request $request){
        try {
            $email=$request->input('email') ?? null;
            if(!$email){
                return ["message"=>"Correo no enviado","status"=>404];
            }
            $user=User::where('email',$email)->first();
            if(!$user){
                return ["message"=>"Usuario no encontrado","status"=>404];
            }
            $user->recovery_cod=rand(10000,10000000);
            $user->save();
            $envio=$this->sendMail($user->email,30548322,[
                "codigo" => $user->recovery_cod,
            ]);
            return [
                'message'=>"Correo enviado ah ".$email
            ];
        } catch (\Exception $e) {
            return $this->HelpError($e);
        }
    }

	public function validateRecovery(Request $request){
        try {
            $email=$request->input('email') ?? null;
            $code=$request->input('code') ?? null;
            if(!$email){
                return response()->json(["message"=>"Correo no enviado","status"=>404],404);
            
            }
            if(!$code){
                return response()->json(["message"=>"Codigo no enviado","status"=>404],404);
            }
            $user=User::where('email',$email)->where('recovery_cod',$code)->first();
            if(!$user){
                return response()->json(["message"=>"Usuario no encontrado","status"=>404],404);
            }
            return [
                'message'=>"Usuario y codigo validos",
                'status'=>200
            ];
        } catch (\Exception $e) {
            return $this->HelpError($e);
        }
    }
        public function putRecovery(Request $request){
        try {
            $recovery_cod=$request->input('recovery_cod') ?? null;
            if(!$recovery_cod){
		return ["message"=>"Codigo no encontrado","status"=>404];
            }
            $password=$request->input('password') ?? null;
            if(!$password){
		return ["message"=>"Contraseña no encontrado","status"=>404];
            }
            $email=$request->input('email') ?? null;
            if(!$email){
		return ["message"=>"Email no encontrado","status"=>404];
            }
            $user=User::where('email',$email)->where('recovery_cod',$recovery_cod)->first();
            if(!$user){
		return ["message"=>"Usuario no encontrado","status"=>404];
            }
            $user->password=bcrypt($password);
            $user->recovery_cod="";
            $user->save();
            return [
                "message"=>"Usuario Actualizado con exito"
            ];
        } catch (\Exception $e) {
            return $this->HelpError($e);
        }
    }
    public function index(Request $request){
        $query=User::query();
        $rol=$request->input('rol') ?? null;
        $name=$request->input('name') ?? null;
        $active=$request->input('active') ?? null;
        $selected=$request->input('selected') ?? null;
        //cargar relations
        $with=$request->input('with') ?? ['membresia'];
        $query->with($with);
        if($rol){
            $query->where('role_id',$rol);
        }
        if($name){
            $query->where('name','like','%'.$name.'%');
        }
        if($active){
            $query->where('active',$active);
        }
        if($selected){
            $query->select(['id','name']);
            return [
                "data"=>$query->get()
            ];
        }
        return $this->HelpPaginate(
                $query
            );
    }
    public function update($id,Request $request){
        try {

            DB::beginTransaction();
                $data=$request->except(['password']);
                $password=$request->input('password') ?? null;

                $user=User::find($id);
                if(!$user){
                    return response()->json(['error'=>'usuario no encontrado'], 400);
                }
                if( isset($password) && $password!=''){
                    $data["password"]=bcrypt($password);
                }
                
                if($request->hasFile('img')){

                    $date = Carbon::now();
                    $text = $date->format('Y_m_d');
                    $image = $request->file('img');
                    $path = $image->store('public/images/users/'.$text."/");
                    $data["img_url"]=env('APP_URL').Storage::url($path);
                    
                }
                $user->update($data);
            DB::commit();
            return response()->json([
                "data"=>$user
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->HelpError($e);
        }
    }
    public function show($id,Request $request){
        $code=$request->input('code') ?? null;
        $includes=$request->input('includes') ?? [];
        if($code){
            $user=User::where('code',$code)->first();
            if(!$user){
                return response()->json([
                    'error'=>"codigo invalido"
                ],404);
            }
            
            $id=$user->id;
        }
        return $this->HelpShow(
            User::where("id",$id)->limit(1)->with($includes),
            $request->all()
        );
    }
    public function delete($id,Request $request){
        return $this->HelpDelete(
            User::where("id",$id)->limit(1),
            $request->all()
        );
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        
        $user=User::where('email',$request->input('email'))->limit(1)->with(['membresia'])->first();
        return [
            'token'=>$token,
            'user'=>$user
        ];
        
    }
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return response()->json(['token_expired'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                    return response()->json(['token_invalid'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return response()->json(['token_absent'], $e->getStatusCode());
            }
            return response()->json(compact('user'));
    }
	public function register(Request $request)
    {
        try {
            DB::beginTransaction();
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:2',
                ]);
                if($validator->fails()){
                    return response()->json($validator->errors()->toJson(), 400);
                }
                $data=$request->except(["active"]);
                $data["password"]=bcrypt($data["password"] ?? "12345");

                try {
                    if(!$auth_user = JWTAuth::parseToken()->authenticate()) {
                        if($auth_user->role_id!=1 && $request->input('role_id')==1){
                            return response()->json([
                                "error"=>"Solo un Admin puede registrar un administrador"
                            ], 400);
                        }//endif
                    }//endif
                } catch (JWTException $e) {
                    if($request->input('role_id')==1){
                       return response()->json(['error' => 'could_not_create_token'], 500); 
                    }
                }
                if(!$data['role_id']){
                    throw new \Exception("Rol no enviado", 404);
                    
                }
                $user = User::create($data);
                $user->code=$user->id.'-'.rand(1000,9999);
                $user->save();
                if($request->has('img')){
                    $date = Carbon::now();
                    $text = $date->format('Y_m_d');
                    $image = $request->file('img');
                    $path = $image->store('public/images/users/'.$text."/");
                    $user->img_url=env('APP_URL').Storage::url($path);
                    $user->save();
                }
                $token = JWTAuth::fromUser($user);
                //conditional case
                if($data['role_id']==3){
                    //init carbon
                    $nowTimeDate = Carbon::now();
                    //create membresia
                    $membresia=membresia::create([
                        'user_id'=>$user->id,
                        'fecha_cobro'=>$nowTimeDate,
                        'type'=>"permitir_gratuita"
                    ]);
                }
                //enviar correo si es valido 0406f231-9899-47d3-a951-f20006e66c25
                try {
                    $enviado=$this->sendMail($user->email,31306527,[
                        "user_name"=>$request->input("name")." ".$request->input("last_name")
                    ]);
                    if($enviado->message!="OK"){
                        throw new \Exception("Error", 404);
                        
                    }
                } catch (\Exception $e) {
                    return $e;
                    return response()->json([
                       "message"=>"Por favor, colocar un correo real"
                    ],404);
                }
            DB::commit();
                return response()->json([
                    'user'=>User::where('id',$user->id)->with(['membresia'])->first(),
                    'token'=>$token
                ],201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->HelpError($e);
        }

    }//Registro

}//End Class
