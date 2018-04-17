<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <link rel="stylesheet" type="text/css" href="css/estilos_lista.css">
</head>
<body>
<div id='pagina'>
    <div id='cabecalho'>
        <?php include('cabecalho.php'); ?>
    </div>
    <div id='menu'>
        <?php include('menu.php'); ?>
    </div>
    <div id='conteudo'>
        <h1>Lista de Participantes</h1>

        <?php
        require_once('InputValidation/CBaseFormValidation.php');

        $ReferenciaConfirmacao = $_REQUEST['ref'];
        $SituacaoInscricao = $_REQUEST['sit'];
        $dbName = 'jei2017_php_mysql';
        $dbPass = '';
        $dbUser = 'root';
        $dbHost = 'localhost';
        $dbPort = '3306';
        $dbTableName = 'participantes';

        try {
            $options = array(1002 => 'SET NAMES UTF8');
            $ligacao = new PDO("mysql:host={$dbHost}; dbname={$dbName}; port={$dbPort}", $dbUser, $dbPass, $options);
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $pe) {
            echo($pe->getMessage());
        }
        $data = date("Y/m/d G:i:s", time());
        try {
            if ($SituacaoInscricao == 'cancel')
                $SituacaoInscricao = 'NAO CONFIRMADA';
            else
                $SituacaoInscricao = 'CONFIRMADA';

            $sql = "UPDATE $dbTableName SET SituacaoInscricao = :SituacaoInscricao, data_confirmacao = :data_confirmacao WHERE ReferenciaConfirmacao = :ReferenciaConfirmacao";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':SituacaoInscricao', $SituacaoInscricao);
            $stmt->bindParam(':ReferenciaConfirmacao', $ReferenciaConfirmacao);
            $stmt->bindParam(':data_confirmacao', $data);
            $stmt->execute();
			echo "<h1 style='color:green'>A inscrição foi confirmada com sucesso.</h1>";
        } catch (PDOException $e) {
            //$e->getMessage();
			echo "<h1 style='color:red'>Não foi possível confirmar a sua inscrição. Tenete mais tarde.</h1>";
        }
		echo "<p>Vai ser redirecionado para a lista de participantes dentro de 10 segundos</p>";
        header("Refresh: 10; url=Participants_lista.php");
        ?>
</body>
</html>