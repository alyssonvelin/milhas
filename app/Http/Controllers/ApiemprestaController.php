<?php

namespace App\Http\Controllers;

use App\Models\Apiempresta;
use Illuminate\Http\Request;

class ApiemprestaController extends Controller
{
    
    /**
     * Index action.
     * @return JSON
     */
    public function index(Request $request)
    {
        $data = $request->all();
        if(!$this->verifyValue($data))
        {
            return response()->json([
                'message' => 'Parâmetro não encontrado',
            ], 400);
        }
        
        $obj = new Apiempresta();
        $return = $this->formatRequired($obj,$data);

        $valor = $data['valor_emprestimo'];
        $arr = array();
        if($return['inst'] && $return['tax'] && $valor)
        {
            foreach($return['inst'] as $valueIns)
            {
                foreach($return['tax'] as $valueTax)
                {
                    if($valueIns['chave'] == $valueTax['instituicao'])
                    {
                        $valorForm = str_replace(array(".",","),array("","."),$valor);
                        if($return['conv'] && in_array($valueTax['convenio'],$return['conv']))
                        {
                            $arr[$valueIns['chave']][] = array('taxa'=>$valueTax['taxaJuros'],
                                                               'valor_parcela'=>number_format($valorForm * $valueTax['coeficiente'],2,',','.'),
                                                               'parcelas'=>$valueTax['parcelas'],
                                                               'convenio'=>$valueTax['convenio']);
                        }
                        else if(!$return['conv'])
                        {
                            $arr[$valueIns['chave']][] = array('taxa'=>$valueTax['taxaJuros'],
                                                               'valor_parcela'=>number_format($valorForm * $valueTax['coeficiente'],2,',','.'),
                                                               'parcelas'=>$valueTax['parcelas'],
                                                               'convenio'=>$valueTax['convenio']);
                        }
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * Load content
     * @return array
     */
    public function instituicoes()
    {
        $obj = new Apiempresta();
        return json_decode($obj->getFile('instituicoes.json',$this->getPath()));
    }

    /**
     * Load content
     * @return array
     */
    public function convenios()
    {
        $obj = new Apiempresta();
        return json_decode($obj->getFile('convenios.json',$this->getPath()));
    }

    /**
     * Verify required value
     * @param array $data
     * @return bool
     */
    public function verifyValue($data)
    {
        if(!array_key_exists('valor_emprestimo',$data) || $data['valor_emprestimo'] < 1)
        {
            return false;
        }
        return true;
    }

    /**
     * Return path source files
     * @return string
     */
    public function getPath()
    {
        return "/app/public/empresta/";
    }

    /**
     * Verify/format filters
     * @param object $obj
     * @param array $data
     * @return array
     */
    public function formatRequired($obj,$data)
    {
        $tax = json_decode($obj->getFile('taxas_instituicoes.json',$this->getPath()),true);
        if(array_key_exists('instituicoes',$data))
        {
            foreach($data['instituicoes'] as $key => $value)
                $data['instituicoes'][$key] = array('chave'=>$value);

            $inst = $data['instituicoes'];
        }
        else
            $inst = json_decode($obj->getFile('instituicoes.json',$this->getPath()),true);

        $conv = false;
        if(array_key_exists('convenios',$data))
            $conv = $data['convenios'];

        if(array_key_exists('parcela',$data))
        {
            foreach($tax as $key => $value)
            {
                if($data['parcela'] != $value['parcelas'])
                    unset($tax[$key]);
            }
        }
        return array('tax'=>$tax,'inst'=>$inst,'conv'=>$conv);
    }

}
