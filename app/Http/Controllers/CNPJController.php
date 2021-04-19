<?php

namespace App\Http\Controllers;

use App\Model\CNPJModel;
use App\Models\CNPJModel as ModelsCNPJModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CNPJController extends Controller
{

    private $url = "https://www.receitaws.com.br/v1/cnpj/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!empty($request->qtd) && !empty($request->cnpj)) {

            $objTurma = ModelsCNPJModel::where('cnpj', $request->cnpj)->simplePaginate($request->qtd);
        } elseif (!empty($request->qtd)) {
            $objTurma = ModelsCNPJModel::orderBy('id')->simplePaginate($request->qtd);
        } else {
            $objTurma = ModelsCNPJModel::orderBy('id')->simplePaginate(2);
        }
        $objItems = [];

        foreach ($objTurma as $item) {
            sleep(20);
            $promise = Http::async()->get($this->url . str_pad($item->cnpj, 14, "0", STR_PAD_LEFT))->then(
                function ($response) {
                    return json_decode(($response->getBody()->getContents()));
                },
                function ($exception) {
                    return $exception->getMessage();
                }
            );
            $response = $promise->wait();
            $objItems[] = $response;
        }

        return view('list')->with(['items' => $objItems, "cnpjs" => $objTurma]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $objCursos = CursoModel::orderBy('id')->get();

        return view('turma.create')->with('cursos', $objCursos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = Http::post($this->url . "store", [
            'nome' => $request->nome,
            'sigla' => $request->sigla,
            'curso_id' => $request->curso_id,

        ]);

        return redirect()->action('TurmaController@index')
            ->with('success', 'Turma foi registrada com sucesso.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TurmaModel  $turmaModel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = Http::get($this->url . $id);

        $objTurma = json_decode(json_encode($response->json()));

        $objCursos = CursoModel::orderBy('id')->get();


        return view('turma.edit')->with(['turma' => $objTurma, "cursos" => $objCursos]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TurmaModel  $turmaModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $response = Http::put($this->url . "update/" . $request->id, [
            'nome' => $request->nome,
            'sigla' => $request->sigla,
            'curso_id' => $request->curso_id,

        ]);

        return redirect()->action('TurmaController@index')
            ->with('success', 'Turma foi alterada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TurmaModel  $turmaModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = Http::delete($this->url . $id);

        return redirect()->action('TurmaController@index')
            ->with('success', 'Turma removida com sucesso.');
    }

    public function search(Request $request)
    {
        $response = Http::post($this->url . "search", [
            'nome' => $request->nome,
        ]);

        $objTurma = json_decode(json_encode($response->json()));

        return view('turma.list')->with('turmas', $objTurma);
    }
}
