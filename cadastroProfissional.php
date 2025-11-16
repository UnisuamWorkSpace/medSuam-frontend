<?php
    include 'dbMedsuam.php';

    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $nomeMedico = mysqli_real_escape_string($conn, $_POST['nomeProfissional']);
        $nomeMedico = ucwords(strtolower($nomeMedico));
        
        /* Faz o email ficar em lowercase */
        $emailMedico = trim($_POST['emailProfissional']);                    // remove leading/trailing spaces
        $emailMedico = strtolower($emailMedico);               // normalize to lowercase
        $emailMedico = mysqli_real_escape_string($conn, $emailMedico); // escape for SQL

        $cpfMedico = mysqli_real_escape_string($conn, $_POST['cpfProfissional']);

        $digitsOnly = preg_replace('/\D/', '', $cpfMedico);
        $senhaMedico = substr($digitsOnly, 0, 8);
        $password_hash = password_hash($senhaMedico, PASSWORD_DEFAULT);

        $aniversarioMedico = mysqli_real_escape_string($conn, $_POST['aniversarioMedico']); 
        $generoMedico =  mysqli_real_escape_string($conn, $_POST['generoMedico']); 
        $celularMedico = mysqli_real_escape_string($conn, $_POST['celularProfissional']);

        $numeroLimpo = preg_replace('/\D/', '', $celularMedico);
        $ddd = substr($numeroLimpo, 0, 2);
        $numero = substr($numeroLimpo, 2);

        $crmMedico = mysqli_real_escape_string($conn, $_POST['documentoProfissional']);
        $estadoCrmMedico = mysqli_real_escape_string($conn, $_POST['estadoprofissional']); 
        $especialidadeMedico = mysqli_real_escape_string($conn, $_POST['especialidadeProfissional']); 

        $sql = "SELECT * FROM medico WHERE email_medico ='$emailMedico' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM medico WHERE cpf_medico ='$cpfMedico' LIMIT 1";
        $result2 = mysqli_query($conn, $sql2);

        $sql3 = "SELECT * FROM paciente WHERE email_paciente ='$emailMedico' LIMIT 1";
        $result3 = mysqli_query($conn, $sql3);

        if(mysqli_num_rows($result) === 1 || mysqli_num_rows($result2) === 1 || mysqli_num_rows($result3) === 1) {
            $status = (mysqli_num_rows($result) === 1 ? '1' : '0') . (mysqli_num_rows($result2) === 1 ? '1' : '0') . (mysqli_num_rows($result3) === 1 ? '1' : '0');

                 switch ($status) {
    case '000': // nothing found
        $errorEmail = '';
        $errorCpf = '';
        break;

    case '100': // only medico email exists
        $errorEmail = "Email já cadastrado !";
        $errorCpf = '';
        break;

    case '010': // only medico CPF exists
        $errorCpf = "CPF já cadastrado !";
        $errorEmail = '';
        break;

    case '001': // only paciente email exists
        $errorEmail = "Email já cadastrado !";
        $errorCpf = '';
        break;

    case '110': // medico email + medico CPF exist
        $errorEmail = "Email já cadastrado !";
        $errorCpf = "CPF já cadastrado !";
        break;

    case '101': // medico email + paciente email exist
        $errorEmail = "Email já cadastrado !";
        $errorCpf = '';
        break;

    case '011': // medico CPF + paciente email exist
        $errorEmail = "Email já cadastrado !";
        $errorCpf = "CPF já cadastrado !";
        break;

    case '111': // everything exists
        $errorEmail = "Email já cadastrado !";
        $errorCpf = "CPF já cadastrado !";
        break;
}
        }else {
            $sql = "INSERT INTO medico (nome_medico, crm, senha_medico, email_medico, sexo_medico, data_nasc_medico, cpf_medico, crm_estado ) VALUES ('$nomeMedico','$crmMedico', '$password_hash', '$emailMedico','$generoMedico','$aniversarioMedico', '$cpfMedico', '$estadoCrmMedico' )" ;
            if(mysqli_query($conn, $sql)) {
                 echo "paciente INSERTED";
            }else {
                echo 'paciente deu erro';
            }

            $sql = "SELECT * FROM medico WHERE email_medico='$emailMedico' LIMIT 1";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) === 1) {
                        $account = mysqli_fetch_assoc($result);

                        $sql = "INSERT INTO telefone (dd, telefone, medico_id_medico) VALUES('$ddd', '$numero', '{$account['id_medico']}')";
                        if(mysqli_query($conn, $sql)) {
                            echo "telefone INSERTED";
                        }else {
                            echo 'telefone deu erro';
                        }
                    }

             $sql = "SELECT * FROM medico WHERE email_medico='$emailMedico' LIMIT 1";
                    $result = mysqli_query($conn, $sql);

                    if(mysqli_num_rows($result) === 1) {
                        $account = mysqli_fetch_assoc($result);

                        $sql = "INSERT INTO especialidade (id_medico, nome) VALUES('{$account['id_medico']}', '$especialidadeMedico' )";
                        if(mysqli_query($conn, $sql)) {
                            echo "especialidade INSERTED";
                        }else {
                            echo 'especialidade deu erro';
                        }
                    }
            header('location: login.php');
            exit;
        
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedSuam</title>
    <link rel="icon" href="./images/ChatGPT Image 11 de out. de 2025, 20_18_05 (1).png">
    <link rel="stylesheet" href="./css/cadastroProfissional.css"/>
</head>
<body>
    <main>
        <div class="formContainer">
            <a href="./index.html"><img class="logoForm" src="./images/logo_branco.png"/></a>
            <h1>Você é um profissional da saúde? Utilize nossa plataforma!</h1>

            <label class="labelStyle mainLabel">Preencha o formulário abaixo para nós entrarmos em contato com instruções.</label>
        
            <form id="profissionalForm" action="./cadastroProfissional.php" method="post">  
                <div class="inputContainer">
                    <input class="inputStyle" type="text"  id="nomeProfissional" name="nomeProfissional" maxlength="70" placeholder="Nome Completo" onkeyup="soLetras(event)" required>
                    <input class="inputStyle noMarginBot " type="text"  id="emailProfissional" name="emailProfissional" placeholder="Email" onblur="validarEmail()" required>
                    <span  id="emailProfissionalSpan" class="spanStyle">
                        <?php if(isset($errorEmail)): ?>
                            <?php echo $errorEmail?>
                            <?php endif;?>
                    </span>
                    <input class="inputStyle noMarginBot" type="text"  id="cpfProfissional" name="cpfProfissional" placeholder="CPF" maxlength="14"  onkeyup="cpfMask(event)" required>
                    <span  id="cpfProfissionalSpan" class="spanStyle">
                        <?php if(isset($errorCpf)): ?>
                            <?php echo $errorCpf?>
                            <?php endif;?>
                    </span>
                     <input class="inputStyle birthday" type="date"  id="aniversariocliente" name="aniversarioMedico"  onblur="validarDataNascimento()" required>
                     <span  id="dateSpan" class="spanStyle"></span>
                    <select class="inputStyle" id="generocliente" name="generoMedico" required>
                            <option value="" disabled selected>Sexo</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                        </select>
                    <input class="inputStyle" type="text"  id="celularProfissional" name="celularProfissional" placeholder="Celular" maxlength="15" required>
                    <input id="documentoprofissional" class="inputStyle" type="text" name="documentoProfissional" placeholder="CRM / CRP / CRN ou CRMV" required>
                    
                    <select id="estadoprofissional" class="inputStyle" name="estadoprofissional" required>
                        <option value="" disabled selected>Estado do Conselho Emissor</option>
                        <option value="AC">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amapá</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Ceará</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Espírito Santo</option>
                        <option value="GO">Goiás</option>
                        <option value="MA">Maranhão</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Pará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PR">Paraná</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piauí</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rondônia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">São Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                    </select>

                    <select name="especialidadeProfissional" id="especialidadeProfissional" class="inputStyle" required>
                        <option value="" disabled selected>Especialidade (deve possuir RQE)</option>
                        <option value="Acupuntura">Acupuntura</option>
                        <option value="AlergiaEImunologia">Alergia e imunologia</option>
                        <option value="Anestesiologia">Anestesiologia</option>
                        <option value="Angiologia">Angiologia</option>
                        <option value="Audiologia">Audiologia</option>
                        <option value="Cardiologia">Cardiologia</option>
                        <option value="Cardiopediatria">Cardiopediatria</option>
                        <option value="CirurgiaCardiovascular">Cirurgia cardiovascular</option>
                        <option value="CirurgiaDaMao">Cirurgia da mão</option>
                        <option value="CirurgiaDeCabeçaEPescoço">Cirurgia de cabeça e pescoço</option>
                        <option value="CirurgiaDoAparelhoDigestivo">Cirurgia do aparelho digestivo</option>
                        <option value="CirurgiaGeral">Cirurgia geral</option>
                        <option value="CirurgiaOncologica">Cirurgia oncológica</option>
                        <option value="CirurgiaPediatrica">Cirurgia pediátrica</option>
                        <option value="CirurgiaPlastica">Cirurgia plástica</option>
                        <option value="CirurgiaToracica">Cirurgia torácica</option>
                        <option value="CirurgiaVascular">Cirurgia vascular</option>
                        <option value="ClinicaGeral">Clínica geral</option>
                        <option value="ClinicaMedica">Clínica médica</option>
                        <option value="Coloproctologia">Coloproctologia</option>
                        <option value="Dermatologia">Dermatologia</option>
                        <option value="EndocrinologiaEMetabologia">Endocrinologia e metabologia</option>
                        <option value="Endocrinopediatria">Endocrinopediatria</option>
                        <option value="Endoscopia">Endoscopia</option>
                        <option value="Fisiatria">Fisiatria</option>
                        <option value="Fisioterapia">Fisioterapia</option>
                        <option value="Fonoaudiologia">Fonoaudiologia</option>
                        <option value="Gastroenterologia">Gastroenterologia</option>
                        <option value="Gastropediatria">Gastropediatria</option>
                        <option value="GenéticaMedica">Genética médica</option>
                        <option value="Geriatria">Geriatria</option>
                        <option value="GinecologiaEObstetricia">Ginecologia e obstetrícia</option>
                        <option value="HematologiaEHemoterapia">Hematologia e hemoterapia</option>
                        <option value="Homeopatia">Homeopatia</option>
                        <option value="Infectologia">Infectologia</option>
                        <option value="Mastologia">Mastologia</option>
                        <option value="MedicinaDeEmergencia">Medicina de emergência</option>
                        <option value="MedicinaDeFamiliaEComunidade">Medicina de família e comunidade</option>
                        <option value="MedicinaDeTrafego">Medicina de tráfego</option>
                        <option value="MedicinaDoSono">Medicina do Sono</option>
                        <option value="MedicinaDoTrabalho">Medicina do trabalho</option>
                        <option value="MedicinaEsportiva">Medicina esportiva</option>
                        <option value="MedicinaFisicaEReabilitacao">Medicina física e reabilitação</option>
                        <option value="MedicinaIntensiva">Medicina intensiva</option>
                        <option value="MedicinaLegalEPericiaMedica">Medicina legal e perícia médica</option>
                        <option value="MedicinaNuclear">Medicina nuclear</option>
                        <option value="MedicinaPreventivaESocial">Medicina preventiva e social</option>
                        <option value="Nefrologia">Nefrologia</option>
                        <option value="Neurocirurgia">Neurocirurgia</option>
                        <option value="Neurologia">Neurologia</option>
                        <option value="Neuropediatria">Neuropediatria</option>
                        <option value="Nutrologia">Nutrologia</option>
                        <option value="Odontologia">Odontologia</option>
                        <option value="Oftalmologia">Oftalmologia</option>
                        <option value="OncologiaClinica">Oncologia clínica</option>
                        <option value="OrtopediaETraumatologia">Ortopedia e traumatologia</option>
                        <option value="Otorrinolaringologia">Otorrinolaringologia</option>
                        <option value="Patologia">Patologia</option>
                        <option value="PatologiaClínicaOUMedicinaLaboratorial">Patologia clínica/medicina laboratorial</option>
                        <option value="Pediatria">Pediatria</option>
                        <option value="Pneumologia">Pneumologia</option>
                        <option value="Pneumopediatria">Pneumopediatria</option>
                        <option value="PrescritorDeCBD">Prescritor de CBD</option>
                        <option value="Psicologia">Psicologia</option>
                        <option value="Psiquiatria">Psiquiatria</option>
                        <option value="RadiologiaEDiagnósticoPorImagem">Radiologia e diagnóstico por imagem</option>
                        <option value="Radioterapia">Radioterapia</option>
                        <option value="Reumatologia">Reumatologia</option>
                        <option value="SaudeMental">Saúde Mental</option>
                        <option value="Urologia">Urologia</option>
                        <option value="Veterinaria">Veterinária</option>
                        <option value="outro">Outro</option>
                    </select>
     
                    <input id="enviarBtn" class="buttonStyle" type="submit" value="Enviar">
                        
                </div>
            </form>
        </div>
        
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
    <script src="./js/cadastroProfissional.js"></script>

</body>
</html>