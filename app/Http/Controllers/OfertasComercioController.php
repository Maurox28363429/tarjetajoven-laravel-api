<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    ofertas_comercio as Models
};
use App\Http\Traits\HelpersTrait;
class OfertasComercioController extends Controller
{
    use HelpersTrait;
    public function index(Request $request){
        $query=Models::query();
        $with=$request->input('with') ?? [];
        $query->with($with);
        $nombre=$request->input('nombre') ?? null;
        if($nombre){
            $query->where('nombre','like',"%".$nombre."%");
        }
        $active=$request->input('active') ?? null;
        if($active){
            $query->where('active',$active);
        }
        $comercio_id=$request->input('comercio_id') ?? null;
        if($comercio_id){
            $query->where('comercio_id',$comercio_id);
        }
        //total
        $init_total=$request->input('init_total') ?? null;
        if($init_total){
            $query->where("total",">=",$init_total);
        }//end
        $end_total=$request->input('end_total') ?? null;
        if($end_total){
            $query->where("total","<=",$end_total);
        }//end
        $descuento=$request->input('descuento') ?? null;
        if($descuento){
            $query->query('descuento',$descuento);
        }//end
        $dir=$request->input('dir') ??  null;
        if($dir && $dir!='todos'){
            $query->orderBy("prioridad","asc");
            $query->whereJsonContains('link_map', ['ubication' => $dir]);
        }
        return $this->HelpPaginate(
                $query,
                10
            );
    }
    public function show($id,Request $request){
        $includes=$request->input('with') ?? [];
        return $this->HelpShow(
            Models::where("id",$id)->limit(1)->with($includes),
            $request->all()
        );
    }
    public function store(Request $request){
        $data=$request->all();
        return $this->HelpStore(
            Models::query(),
            $data
        );
    }
    public function update($id,Request $request){
        return $this->HelpUpdate(
            Models::where("id",$id)->limit(1),
            $request->all()
        );
    }
    public function delete($id,Request $request){
        return $this->HelpDelete(
            Models::where("id",$id)->limit(1),
            $request->all()
        );
    }
}
