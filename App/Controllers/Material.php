<?php
 
namespace App\Controllers;
 
use SON\Controller\Action;
use \SON\Di\Container;
use \SON\Init\Bootstrap;
 
class Material extends Action
{
    protected $view;    
    public function __construct()
    {
        $this->view = new \stdClass();
    }
 
    /**
     * MÉTODOS DO CONTROLLER CLIENTES
     * */    
 
    // Exibe a relação de clientes (SELECT)
    public function materialListar()
    {
      
    // Diz ao Controller que utilizaremos a Model Cliente (tabela cliente)        
    $material = Container::getClass("Material");
 
    /**
    * O Método select, da classe Table, retorna um array com o resultado do select, considerando o que foi passado como parâmetro. 
    * Neste caso, passamos os seguintes parâmetros: 
    * @param $campos = Informa quais os campos farão parte do select. Também pode ser usado *, para selecionar todos os campos
    * @param $ordenarPor = Informa por campo será feita a ordenação e
    * @param $ordenacao = Informa qual a ordem: ASC ou DESC
    * Opcional: você ainda pode informar um quarto parâmetro, quando necessário.
    * Ex.: $condicoes = "WHERE id > 20";
    * Neste caso, bastaria adicionar o parâmetro $condicoes após $ordenacao
    */
 
    $campos = "*";
    $ordenarPor = "nome_produto";
    $ordenacao = "asc";
    $materiais = $material->select($campos, $ordenarPor, $ordenacao);
 
    // Envia o array de clientes do select acima para a view; 
    // Na view, faremos um for para exibir todos os dados de clientes deste array
    $this->view->materiais = $materiais;

        
    // Renderizando (chama a view: cliente/cliente.phtml). Esta view recebe o array $this->view->clientes. 
    //Ele será o responsável por exibir os dados na view
    $this->render('material');
    }
 
    // Exibe o formulário de cadastro de clientes
    public function materialCadastrar()
    {
        /** 
        * Renderizando. 
        * Chama a view cliente/cliente-cadastrar.phtml 
        * Caso não queira utilizar o template e imprimir apenas o content, usar $this->render('cliente-cadastrar', false);
        * Todo o conteúdo desta view (neste caso, o formulário de cadastro) será impresso, no layout, através do código echo $this->content(); que já está definido no arquivo layout.phtml
        */

         // Diz ao Controller que utilizaremos a Model Categoria  
        $categoria = Container::getClass("Categoria");
        $categorias = $categoria->select("*", "nome", "asc");

        $this->view->categorias = $categorias;
   
        $this->render('material-cadastrar');
 
    }
 
    // Salva o formulário de cadastro de clientes (INSERT)
    public function materialCadastrarSalvar()
    {
         // Diz ao Controller que utilizaremos a Model Cliente
        $material = Container::getClass("Material");        
 
        // Array com os campos e valores do BD para realizar o insert.
        // Estes valores foram passados pelo formulário de cadastro, via POST
        $arrayBD = [
        'nome_produto' => $_POST["nome"],
        'descricao' => $_POST["descricao"],
        'id_categoria' => $_POST["categoria"],
        ];

 
        // Chama o método insert() da classe Table.
        // Basta passar o array ($arrayBD) como parâmetro para que o INSERT aconteça.
        // Note que não foi preciso implementar nada em SQL pois tudo já está pronto.
        $materiais = $material->insert($arrayBD);
 
        // Renderizando e chamando a view que mostrará uma mensagem de confirmação
        $this->render('material-cadastrar-salvar');
 
    }
 
    // Exibe o formulário de edição de cliente (SELECT)
    public function materialEditar()
    {
  // Diz ao Controller que utilizaremos a Model Cliente
        $material = Container::getClass("Material");
 
        /**
         * O método findId() da classe Table retorna um array com todos os dados de um cliente e exige dois parâmetros. 
         * O primeiro parâmetro do método é o nome do campo da tabela onde será feita a consulta (neste caso, campo id).
         * O segundo parâmetro é o valor que será buscado neste campo. Neste caso, o valor do id que foi enviado por parâmetro na URL.
         * O método Bootstrap::getIdByUrl() retorna o valor do parâmetro que foi passado na URL (neste caso, o valor do id). 
         * Exemplo: www.projetointegrador.com.br/cliente-editar-salvar/12 informa que o ID do cliente que estamos fazendo a edição é 12
         * */
 
        $materiais = $material->findId("id", Bootstrap::getIdByUrl());
 
        // Atribui para a view;
        $this->view->material = $materiais;

        // Diz ao Controller que utilizaremos a Model Categoria  
        $categoria = Container::getClass("Categoria");
        $categorias = $categoria->select("*", "nome", "asc");

        $this->view->categorias = $categorias;


  
        // Renderizando
        $this->render('material-editar');
    }
 
    // Salva o formulário de edição de cliente (UPDATE)
    public function materialEditarSalvar()
    {
  // Diz ao Controller que utilizaremos a Model Cliente
        $material = Container::getClass("Material");        
 
        // Array com os campos e valores do BD para realizar o update.
        // Estes valores foram passados pelo formulário de edição, via POST
        $arrayDados = [
        'nome_produto' => $_POST["nome_produto"],
        'descricao' => $_POST["descricao"],
        'id_categoria' => $_POST["categoria"],
        ];        
 
        // O método abaixo retorna o ID passado por parâmetro na URL
        $idUrl = Bootstrap::getIdByUrl();
 
        /**
        * Método público para atualizar os dados na tabela 
        * @param $arrayDados = Array de dados contendo colunas e valores que serão editados.
        * @param $arrayCondicao = Array de dados contendo colunas e valores para condição WHERE - Exemplo array('$id='=>1) 
        * Neste caso, estamos passando apenas a condição de que o id deverá ter o valor passado na URL (e recuperado através de $idUrl = Bootstrap::getIdByUrl() )
        */          
 
        $arrayCondicao = array('id='=>$idUrl);
        $material->update($arrayDados, $arrayCondicao);        
 
        // Renderizando
        $this->render('material-editar-salvar');
    }
 
    // Solicita confirmação de exclusão de cliente
    public function materialDeletarConfirmacao()
    {
   // Apenas exibirá a view de confirmação de exclusão
        $this->render('material-deletar-confirmacao');
    }
 
    // Exclui o cliente (DELETE)
    public function materialDeletarSalvar()
    {
 // Diz ao Controller que utilizaremos a Model Cliente
        $material = Container::getClass("Material");
 
        // O método abaixo deleterá o ID passado por parâmetro na URL e pelo método Bootstrap::getIdByUrl();
        // O primeiro parâmetro passado: "id" representa o nome do campo na tabela.
        $material->delete("id", Bootstrap::getIdByUrl());
  
        // Renderizando
        $this->render('material-deletar-salvar');
    }
    // Salva o formulário de cadastro de clientes (INSERT)


 
}