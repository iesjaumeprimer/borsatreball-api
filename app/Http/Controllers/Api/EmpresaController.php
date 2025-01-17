<?php

namespace App\Http\Controllers\Api;

use App\Entities\Empresa;
use App\Http\Resources\EmpresaResource;

/**
 * @OA\Get(
 * path="/api/empresas",
 * summary="Dades de les empreses",
 * description="Torna les dades de les empreses",
 * operationId="indexEmpreses",
 * tags={"empreses"},
 * security={ {"apiAuth": {} }},
 * @OA\Response(
 *    response=200,
 *    description="Empreses segons el permis",
 *    @OA\JsonContent(
 *        @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/EmpresaResource")
 *        )
 *    )
 *   )
 * )
 */

/**
 * @OA\Post(
 * path="/api/empresas",
 * summary="Guardar empresa",
 * description="Guardar perfil empresa",
 * operationId="storeEmpreses",
 * tags={"empreses"},
 * security={ {"apiAuth": {} }},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(ref="#/components/schemas/EmpresaStoreRequest")
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Empresa correctament donada d'alta",
 *    @OA\JsonContent(ref="#/components/schemas/EmpresaResource")
 * ),
 * @OA\Response(
 *    response=422,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Credencials no vàlides")
 *        )
 *     )
 * )
 */

/**
 * @OA\Put(
 * path="/api/empresas/{id}",
 * summary="Modificar empresa",
 * description="Modificar perfil empresa",
 * operationId="updateEmpreses",
 * tags={"empreses"},
 * security={ {"apiAuth": {} }},
 * @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 * ),
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(ref="#/components/schemas/EmpresaStoreRequest")
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Empresa correctament donat d'alta",
 *    @OA\JsonContent(ref="#/components/schemas/EmpresaResource")
 * ),
 * @OA\Response(
 *    response=422,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Credencials no vàlides")
 *        )
 *     )
 * )
 */

/**
 * @OA\Get(
 * path="/api/empresas/{id}",
 * summary="Dades d'una empresa",
 * description="Torna les dades d'una empresa",
 * operationId="showEmpresa",
 * tags={"empreses"},
 * security={ {"apiAuth": {} }},
 * @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Empresa si te permis",
 *    @OA\JsonContent(
 *        @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/EmpresaResource")
 *        )
 *    )
 *   )
 * )
 */


class EmpresaController extends ApiBaseController
{

    public function model(){
        return 'Empresa';
    }

    public function destroy($id)
    {
        $thereIsAndOffer = Empresa::find($id)->Ofertas()->where('archivada',0)->count();
        if ($thereIsAndOffer) return response("L'empresa té ofertes. Esborra-les primer",400);

        if (Empresa::destroy($id)) return response(1,200);

        return response("No he pogut Esborrar $id",400);
    }

    public function index()
    {
        if (AuthUser()->isEmpresa())
            return EmpresaResource::collection(Empresa::where('id',AuthUser()->id)->get());
        if (AuthUser()->isAlumno())
            return [];
        if (AuthUser()->isAdmin() || AuthUser()->isResponsable()) return parent::index();
        return response('No autenticado',405);
    }

    public function show($id)
    {
        if (AuthUser()->isAlumno()) return [];
        if (AuthUser()->isEmpresa()) $id = AuthUser()->id;
        if (AuthUser()) return parent::store($id);
    }
}
