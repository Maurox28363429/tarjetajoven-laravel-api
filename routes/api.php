<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//borraro
//Route::get('example', 'App\Http\Controllers\UserController@example');
//auth
Route::post('mau_import',function(Request $request){
    $data=$request->input('data');
    foreach ($data as $key => $value) {
        $fechaNacimiento = $value['FECHA_NACIMIENTO'];
        $fechaNacimiento = Carbon::createFromFormat('m/d/Y', $fechaNacimiento)->format('Y-m-d');

        User::where('email',$value['MAIL'])->update([
        "fecha_nacimiento"=>$fechaNacimiento,
        "beneficiario_poliza_name"=>$value['NOMBRE_BENEFICIARIO'] ?? null,
        "beneficiario_poliza_cedula"=>$value['DNI_BENEFICIARIO'] ?? null,
        "dni_text"=>$value['DNI'] ?? null,
        ]);
    }
});


Route::post("woocommerce",function(Request $request){
    $filename = 'archivo.txt';
    $data="\n".json_encode(
        $request->all()
    );
    if (!file_exists($filename)) {
        // si el archivo no existe, crearlo con el contenido "Iniciando archivo"
        file_put_contents($filename,$data);
    } else {
        // si el archivo existe, agregar "nuevo contenido" al final del archivo
        $contenido_actual = file_get_contents($filename);
        file_put_contents($filename, $contenido_actual . $data);
    }

});
Route::get("woocommerce",function(Request $request){
    $filename = 'archivo.txt';
    $data="\n".json_encode(
        $request->all()
    );
    if (!file_exists($filename)) {
        // si el archivo no existe, crearlo con el contenido "Iniciando archivo"
        file_put_contents($filename,$data);
    } else {
        // si el archivo existe, agregar "nuevo contenido" al final del archivo
        $contenido_actual = file_get_contents($filename);
        file_put_contents($filename, $contenido_actual . $data);
    }

});

Route::post('importUserMembresia', 'App\Http\Controllers\UserController@importUserMembresia');

Route::post('importUpdateUsers', 'App\Http\Controllers\UserController@importUpdateUsers');

Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');

Route::get('export_membresia', 'App\Http\Controllers\UserController@export_membresia');
Route::get('export_pdf_membresia', 'App\Http\Controllers\UserController@export_pdf_membresia');

Route::get('auth', 'App\Http\Controllers\UserController@getAuthenticatedUser');
Route::post('getRecovery', 'App\Http\Controllers\UserController@getRecovery');
Route::post('putRecovery', 'App\Http\Controllers\UserController@putRecovery');
//user
Route::post('validateRecovery', 'App\Http\Controllers\UserController@validateRecovery');
Route::get('user/{id}', 'App\Http\Controllers\UserController@show');
Route::get('export_pdf_seguro/{id}', 'App\Http\Controllers\UserController@export_pdf_seguro');
Route::get('users', 'App\Http\Controllers\UserController@index');
Route::put('user/{id}', 'App\Http\Controllers\UserController@update');
Route::post('user/{id}', 'App\Http\Controllers\UserController@update');
Route::delete('user/{id}', 'App\Http\Controllers\UserController@delete');



Route::get('/export_form2_excel', 'App\Http\Controllers\FormularioController@export_form2_excel');
Route::get('/export_users_excel', 'App\Http\Controllers\UserController@export_users_excel');

Route::get('/export_vendedor_users_excel', 'App\Http\Controllers\UserController@export_vendedor_users_excel');
//Dashboard
Route::get('/dashboard_admin', 'App\Http\Controllers\DashboardController@admin');
Route::get('/dashboard_comercio', 'App\Http\Controllers\DashboardController@comercio');

//Roles
Route::get('roles', 'App\Http\Controllers\RolesController@index');
//formulario
Route::post('/formulario','App\Http\Controllers\clientesController@store');
Route::get('/export_clientes', 'App\Http\Controllers\clientesController@exportar');
Route::get('/report', 'App\Http\Controllers\clientesController@report');
Route::get('/export_clientes_excel', 'App\Http\Controllers\clientesController@exportar_excel');
Route::delete('/client/{id}', 'App\Http\Controllers\clientesController@delete');
Route::put('/client/{id}', 'App\Http\Controllers\clientesController@update');
Route::get('/clientes','App\Http\Controllers\clientesController@index');
Route::get('/cliente/{id}','App\Http\Controllers\clientesController@show');
Route::get('/graficas','App\Http\Controllers\clientesController@graficas');

//PriceMembresia
Route::get('price-membresia',"App\Http\Controllers\PriceMembresiaController@index");
Route::get('price-membresia/{id}',"App\Http\Controllers\PriceMembresiaController@show");
Route::put('price-membresia/{id}',"App\Http\Controllers\PriceMembresiaController@update");
Route::post('price-membresia',"App\Http\Controllers\PriceMembresiaController@store");
Route::delete('price-membresia/{id}',"App\Http\Controllers\PriceMembresiaController@delete");

//SOS
Route::get('sos',"App\Http\Controllers\SosController@index");
Route::get('sos/{id}',"App\Http\Controllers\SosController@show");
Route::put('sos/{id}',"App\Http\Controllers\SosController@update");
Route::post('sos/{id}',"App\Http\Controllers\SosController@update");
Route::post('sos',"App\Http\Controllers\SosController@store");
Route::delete('sos/{id}',"App\Http\Controllers\SosController@delete");
//membresia
Route::get('membresia',"App\Http\Controllers\MembresiaController@index");
Route::get('membresia/{id}',"App\Http\Controllers\MembresiaController@show");
Route::put('membresia/{id}',"App\Http\Controllers\MembresiaController@update");
Route::post('membresia',"App\Http\Controllers\MembresiaController@store");
Route::delete('membresia/{id}',"App\Http\Controllers\MembresiaController@delete");

//Concurso
Route::get('concurso/years',"App\Http\Controllers\ConcursoController@getYears");
Route::get('concurso',"App\Http\Controllers\ConcursoController@index");
Route::get('concurso/{id}',"App\Http\Controllers\ConcursoController@show");
Route::put('concurso/{id}',"App\Http\Controllers\ConcursoController@update");
Route::post('concurso/{id}',"App\Http\Controllers\ConcursoController@update");
Route::post('concurso',"App\Http\Controllers\ConcursoController@store");
Route::delete('concurso/{id}',"App\Http\Controllers\ConcursoController@delete");

//PaymentMembresia
Route::get('payment-membresia',"App\Http\Controllers\PaymentMenbresiaController@index");
Route::get('payment-membresia/{id}',"App\Http\Controllers\PaymentMenbresiaController@show");
Route::get('/export_payment_excel', 'App\Http\Controllers\PaymentMenbresiaController@exportar_excel');
Route::put('payment-membresia/{id}',"App\Http\Controllers\PaymentMenbresiaController@update");
Route::post('payment-membresia',"App\Http\Controllers\PaymentMenbresiaController@store");


Route::get('payment-membresia/forzar/{id}',"App\Http\Controllers\PaymentMenbresiaController@forzar");

Route::get('payment-membresia/forzar_email/{id}',"App\Http\Controllers\PaymentMenbresiaController@wordpress_email_forzar");

Route::post('prueba-membresia',"App\Http\Controllers\PaymentMenbresiaController@regalia");

Route::post("payment-membresia/changes/{id}","App\Http\Controllers\PaymentMenbresiaController@changes");
Route::get("verificar_pago/{id}","App\Http\Controllers\PaymentMenbresiaController@verificar");

Route::delete('payment-membresia/{id}',"App\Http\Controllers\PaymentMenbresiaController@delete");

//ofertas
Route::get('comercio-ofertas',"App\Http\Controllers\OfertasComercioController@index");
Route::get('comercio-ofertas/{id}',"App\Http\Controllers\OfertasComercioController@show");
Route::put('comercio-ofertas/{id}',"App\Http\Controllers\OfertasComercioController@update");
Route::post('comercio-ofertas/{id}',"App\Http\Controllers\OfertasComercioController@update");
Route::post('comercio-ofertas',"App\Http\Controllers\OfertasComercioController@store");
Route::delete('comercio-ofertas/{id}',"App\Http\Controllers\OfertasComercioController@delete");
//universidades
Route::get('universidades',"App\Http\Controllers\UniversidadesController@index");
Route::get('universidades/{id}',"App\Http\Controllers\UniversidadesController@show");
Route::post('universidades/{id}',"App\Http\Controllers\UniversidadesController@update");
Route::put('universidades/{id}',"App\Http\Controllers\UniversidadesController@update");
Route::post('universidades',"App\Http\Controllers\UniversidadesController@store");
Route::delete('universidades/{id}',"App\Http\Controllers\UniversidadesController@delete");
//noticias
Route::get('noticias',"App\Http\Controllers\NoticiasController@index");
Route::get('noticias/{id}',"App\Http\Controllers\NoticiasController@show");
Route::put('noticias/{id}',"App\Http\Controllers\NoticiasController@update");
Route::post('noticias/{id}',"App\Http\Controllers\NoticiasController@update");
Route::post('noticias',"App\Http\Controllers\NoticiasController@store");
Route::delete('noticias/{id}',"App\Http\Controllers\NoticiasController@delete");
//ProductosEcommercesController
Route::get('productoEcommerce',"App\Http\Controllers\ProductosEcommercesController@index");

Route::get('productoEcommerce/{id}',"App\Http\Controllers\ProductosEcommercesController@show");

Route::put('productoEcommerce/{id}',"App\Http\Controllers\ProductosEcommercesController@update");

Route::post('productoEcommerce/{id}',"App\Http\Controllers\ProductosEcommercesController@update");

Route::post('productoEcommerce',"App\Http\Controllers\ProductosEcommercesController@store");

Route::delete('productoEcommerce/{id}',"App\Http\Controllers\ProductosEcommercesController@delete");

//Orden Ecommerce
Route::get('ordenEcommerce',"App\Http\Controllers\OrdenEcommerceController@index");

Route::get('ordenEcommerce/{id}',"App\Http\Controllers\OrdenEcommerceController@show");

Route::put('ordenEcommerce/{id}',"App\Http\Controllers\OrdenEcommerceController@update");

Route::post('ordenEcommerce/{id}',"App\Http\Controllers\OrdenEcommerceController@update");

Route::post('ordenEcommerce',"App\Http\Controllers\OrdenEcommerceController@store");

Route::delete('ordenEcommerce/{id}',"App\Http\Controllers\OrdenEcommerceController@delete");

//ofertas percibidas por los clientes
Route::get('cliente-comercio-ofertas',"App\Http\Controllers\ComercioOfertaClienteController@index");
Route::get('cliente-comercio-ofertas/{id}',"App\Http\Controllers\ComercioOfertaClienteController@show");
Route::put('cliente-comercio-ofertas/{id}',"App\Http\Controllers\ComercioOfertaClienteController@update");
Route::post('cliente-comercio-ofertas',"App\Http\Controllers\ComercioOfertaClienteController@store");
Route::delete('cliente-comercio-ofertas/{id}',"App\Http\Controllers\ComercioOfertaClienteController@delete");
//pronvincias
Route::get('provincias',"App\Http\Controllers\ProvinciasController@index");
Route::get('provincias/{id}',"App\Http\Controllers\ProvinciasController@show");
Route::put('provincias/{id}',"App\Http\Controllers\ProvinciasController@update");
Route::post('provincias',"App\Http\Controllers\ProvinciasController@store");
Route::delete('provincias/{id}',"App\Http\Controllers\ProvinciasController@delete");

//noticias_informativas
Route::get('noticias_informativas',"App\Http\Controllers\NoticiasInformativasController@index");
Route::get('noticias_informativas/{id}',"App\Http\Controllers\NoticiasInformativasController@show");
Route::post('noticias_informativas/{id}',"App\Http\Controllers\NoticiasInformativasController@update");
Route::post('noticias_informativas',"App\Http\Controllers\NoticiasInformativasController@store");
Route::delete('noticias_informativas/{id}',"App\Http\Controllers\NoticiasInformativasController@delete");

//directivos
Route::get('directivos',"App\Http\Controllers\DirectivosController@index");
Route::get('directivos/{id}',"App\Http\Controllers\DirectivosController@show");
Route::post('directivos/{id}',"App\Http\Controllers\DirectivosController@update");
Route::post('directivos',"App\Http\Controllers\DirectivosController@store");
Route::delete('directivos/{id}',"App\Http\Controllers\DirectivosController@delete");

//seguimiento de clientes en comercio
Route::get('tracking',"App\Http\Controllers\TrackingComerioController@index");
Route::get('tracking/{id}',"App\Http\Controllers\TrackingComerioController@show");
Route::post('tracking/{id}',"App\Http\Controllers\TrackingComerioController@update");
Route::post('tracking',"App\Http\Controllers\TrackingComerioController@store");
Route::delete('tracking/{id}',"App\Http\Controllers\TrackingComerioController@delete");
//version_app
Route::get('versionapp',"App\Http\Controllers\VersionappController@get");

Route::post('versionapp',"App\Http\Controllers\VersionappController@update");

//categoria de productos
Route::get('product-category',"App\Http\Controllers\ProductoCategoriasController@index");
Route::get('product-category/{id}',"App\Http\Controllers\ProductoCategoriasController@show");
Route::post('product-category/{id}',"App\Http\Controllers\ProductoCategoriasController@update");
Route::post('product-category',"App\Http\Controllers\ProductoCategoriasController@store");
Route::delete('product-category/{id}',"App\Http\Controllers\ProductoCategoriasController@delete");
//Noticias Pachama
Route::get('noticias_pachama',"App\Http\Controllers\NoticiasPachamaController@index");
Route::get('noticias_pachama/{id}',"App\Http\Controllers\NoticiasPachamaController@show");
Route::post('noticias_pachama/{id}',"App\Http\Controllers\NoticiasPachamaController@update");
Route::post('noticias_pachama',"App\Http\Controllers\NoticiasPachamaController@store");
Route::delete('noticias_pachama/{id}',"App\Http\Controllers\NoticiasPachamaController@delete");

//Notificaciones notify
Route::get('notify',"App\Http\Controllers\NotifyController@index");
Route::get('notify/{id}',"App\Http\Controllers\NotifyController@show");
Route::post('notify/{id}',"App\Http\Controllers\NotifyController@update");
Route::post('notify',"App\Http\Controllers\NotifyController@store");
Route::delete('notify/{id}',"App\Http\Controllers\NotifyController@delete");
//Consecutivos
Route::get('consecutivos',"App\Http\Controllers\ConsecutivosController@index");
Route::get('consecutivos/{id}',"App\Http\Controllers\ConsecutivosController@show");
Route::post('consecutivos/{id}',"App\Http\Controllers\ConsecutivosController@update");
Route::post('consecutivos',"App\Http\Controllers\ConsecutivosController@store");
Route::delete('consecutivos/{id}',"App\Http\Controllers\ConsecutivosController@delete");
//Formulario2
Route::get('formulario2',"App\Http\Controllers\FormularioController@index");
Route::get('formulario2/{id}',"App\Http\Controllers\FormularioController@show");
Route::put('formulario2/{id}',"App\Http\Controllers\FormularioController@update");
Route::post('formulario2',"App\Http\Controllers\FormularioController@store");
Route::delete('formulario2/{id}',"App\Http\Controllers\FormularioController@delete");
