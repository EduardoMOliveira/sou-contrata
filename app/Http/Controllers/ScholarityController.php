<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Scholarity;
use App\Vacancy;
use Illuminate\Support\Facades\Validator;

class ScholarityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('professor.academic-data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $id = !empty($request->session()->get('candidate')) ? $request->session()->get('candidate') : 1 ;

        $school = new Scholarity();
        
        foreach ($request['graduations'] as $k => $d) {

            $validator = Validator::make($request->all(),[

                'file_graduate.*'  => 'required|mimes:application/pdf, application/x-pdf,application/acrobat, applications/vnd.pdf, text/pdf, text/x-pdf|max:10000'
            ]);
            
            dd($validator->messages());

            if ($validator->fails()) {
                dd($validator->messages());
    
                return redirect()
                    ->route('personal-data.index')
                    ->withInput($request->all())
                    ->withErrors($validator->messages())
                ;
            } else {

                switch ($d) {

                    case '1':
                        // se for 1 é Graduação
                        // Documentos de Graduação do Candidato

                        $path_file = $request['file_graduate'][$k]->store("documents-graduate/{$id}");
                        break;

                    case '2':
                        // se for 2 é Mestrado
                        // Documentos de Mestrado do Candidato
                        $path_file = $request['file_graduate'][$k]->store("documents-master/{$id}");
                        break;

                    case '3':
                        // se for 3 é Doutorado
                        // Documentos de Doutorado do Candidato
                        $path_file = $request['file_graduate'][$k]->store("documents-doctorate/{$id}");
                        break;
                
                    }

                $school->class_name = $request->cadlettters;
                $school->end_date = $this->br_to_bank($request->inputDataConclusao[$k]);
                $school->init_date = $this->br_to_bank($request->inputDataConclusao[$k]);
                $school->link = $path_file;
                $school->scholarity_type = $request->inputCursos[$k];
                $school->teaching_institution = $request->inpuInstituicao[$k];
                $school->candidate_id = $id;

                $school->save();
            }
        }

        $resp = $school;
        $data = Vacancy::all()->where('edict_id', 1);

        Helper::alterSession($request, 3);
        return view('vacancy/process', compact(['resp','data']));

    }

    public function br_to_bank($now)
    {
        $data = explode('/', $now);
        $dt = date('Y-m-d', strtotime($now));

        return $dt;
    }

    public function area() {
        $area = ['Engenharia', 'Medicina', 'Advocacia', 'Administração', 'Finança'];
        echo json_encode($area);
    }

    public function subarea($area) {
        $area = (int) $area;
        switch($area) {
            case 0: {
                $subarea = ['Cívil', 'Mecânica'];
                break;
            }
            case 1: {
                $subarea = ['Fisioterapia', 'Biomedicina'];
                break;
            }
            case 2: {
                $subarea = ['Civíl', 'Criminal'];
                break;
            }
            case 3: {
                $subarea = ['Vendas', 'Marketing'];
                break;
            }
            case 4: {
                $subarea = ['Empresarial', 'Contabilidade'];
                break;
            }
        }
        echo json_encode($subarea);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function document_academic(Request $request) {

        //$session = $request->session()->get('candidate');
        //$user = $session[0]->id;

        $input = $request->all();
        $input['image'] = $input['file_graduate'];

        // Pegando a extensão do arquivo

        $arr = explode('.', $input['image']);
        $extensao = end($arr);

        $input['image'] = time() . '.' . $extensao;
        //$request->image->move(public_path("documents-academic/{$user}/"), $input['image']);

    }
}
