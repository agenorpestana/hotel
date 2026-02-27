<?php 
@session_start();
require_once("../../conexao.php");

$id = $_GET['id'];

$enviar = @$_GET['enviar'];

$token_rel = "FKLUY7852";
ob_start();
include("reserva.php");
$html = ob_get_clean();

//CARREGAR DOMPDF

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

use Dompdf\Options;



header("Content-Transfer-Encoding: binary");

header("Content-Type: image/png");



//INICIALIZAR A CLASSE DO DOMPDF

$options = new Options();

$options->set('isRemoteEnabled', TRUE);

$pdf = new DOMPDF($options);





//Definir o tamanho do papel e orientação da página

$pdf->set_paper('A4', 'portrait');



//CARREGAR O CONTEÚDO HTML

$pdf->load_html($html);



//RENDERIZAR O PDF

$pdf->render();

//NOMEAR O PDF GERADO



$output = $pdf->output();

$arquivo = "../pdf/reservas/reserva_".$id.".pdf";

	

if(file_put_contents($arquivo,$output) <> false) {

	$pdf->stream(

	'reserva.pdf',

	array("Attachment" => false)

);



}







$query = $pdo->query("SELECT * from reservas where id = '$id' ");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

$hospede = $res[0]['hospede'];

$ref_pgto = $res[0]['ref_pgto'];



//consulto o pagamento

require("../../pagamentos/consultar_pagamento.php");



$query = $pdo->query("SELECT * from hospedes where id = '$hospede' ");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

$telefone = $res[0]['telefone'];



// Cria uma chave única para identificar essa reserva
$chave_envio = 'enviado_reserva_' . $id;

// Verifica se já enviou antes
if ($enviar == 'sim' && $api_whatsapp == 'Sim' && empty($_SESSION[$chave_envio])) {
    
    // Marca como enviado
    $_SESSION[$chave_envio] = true;

    // Envia só uma vez
    $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone);
    $mensagem = '';
    $url_envio = $url_sistema . "painel/pdf/reservas/reserva_" . $id . ".pdf";

    require("../api/doc.php");
}



 ?>