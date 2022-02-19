<?php

namespace App\Http\Business\Api\Scc\Admin\Usuario;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Usuario\Usuario;

class UsuarioBusinessRule extends MainBusinessRule
{

    private $modelUsuario;

    public function __construct()
    {
        $this->modelUsuario = new Usuario();
    }

    public function listarUsuariosAdmin()
    {
        $usuarios = $this->modelUsuario
            ->select(['id', 'id_pessoa', 'login'])
            ->with(['pessoa:id,nome'])
            ->setPaginateParams([
                // 'filtro' => 'Walter'
            ])
            ->columnsSearch('login')
            ->columnsSearchWith('pessoa:nome,email')
            ->columnsOrderBy(['id' => 'desc'])
            ->columnsOrderByWith(['pessoa:id,nome' => 'desc'])
            ->paginate()->toArray();

        return parent::response()
            ->setData($usuarios)
            ->setMessageSuccess('Usuários listados com sucesso.')
            ->setMessageError('Nenhum usuário encontrado.');
    }
}
