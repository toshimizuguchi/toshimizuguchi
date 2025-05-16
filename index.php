<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Calculadora de Produtos de Academia</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: rgb(185, 58, 77);
    }

    .container {
      max-width: 450px;
      margin: auto;
      background: #d86c7c;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-top: 12px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      box-sizing: border-box;
    }

    button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background-color: purple;
      color: black;
      border: none;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    #resultado {
      margin-top: 25px;
      font-weight: bold;
      text-align: center;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Calculadora de Produtos de Academia</h2>

    <form method="POST">
      <label for="produto">Produto:</label>
      <input type="text" name="produto" placeholder="Ex: Pré-Treino">

      <label for="preco">Preço unitário (R$):</label>
      <input type="number" name="preco" step="0.01" placeholder="Ex: 100,00" required>

      <label for="quantidade">Quantidade:</label>
      <input type="number" name="quantidade" required>

      <label for="desconto">Desconto (%):</label>
      <input type="number" name="desconto" step="0.01">

      <label for="frete">Frete (R$):</label>
      <input type="number" name="frete" step="0.01">

      <label for="imposto">Imposto (%):</label>
      <input type="number" name="imposto" step="0.01">

      <button type="submit" name="calcular">Calcular e Salvar</button>
    </form>

    <div id="resultado">
      <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["calcular"])) {
        $produto = $_POST["produto"] ?? '';
        $preco = floatval(str_replace(',', '.', $_POST["preco"]));
        $quantidade = intval($_POST["quantidade"]);
        $desconto = isset($_POST["desconto"]) ? floatval($_POST["desconto"]) : 0;
        $frete = isset($_POST["frete"]) ? floatval($_POST["frete"]) : 0;
        $imposto = isset($_POST["imposto"]) ? floatval($_POST["imposto"]) : 0;

        if ($preco <= 0 || $quantidade <= 0) {
          echo "Por favor, preencha o preço e a quantidade corretamente.";
        } else {
          $subtotal = $preco * $quantidade;
          $valorDesconto = $subtotal * ($desconto / 100);
          $valorImposto = ($subtotal - $valorDesconto) * ($imposto / 100);
          $totalFinal = $subtotal - $valorDesconto + $valorImposto + $frete;

          $nomeProduto = $produto ? ' para "' . htmlspecialchars($produto) . '"' : "";

          echo "Subtotal: R$ " . number_format($subtotal, 2, ",", ".") . "<br>";
          echo "Desconto ({$desconto}%): -R$ " . number_format($valorDesconto, 2, ",", ".") . "<br>";
          echo "Imposto ({$imposto}%): +R$ " . number_format($valorImposto, 2, ",", ".") . "<br>";
          echo "Frete: +R$ " . number_format($frete, 2, ",", ".") . "<br><br>";
          echo "<strong>Preço final{$nomeProduto}: R$ " . number_format($totalFinal, 2, ",", ".") . "</strong>";

          // SALVANDO EM ARQUIVO
          // Conexão com o banco
$host = "localhost";
$usuario = "root"; // ou seu usuário MySQL
$senha = "";       // senha do MySQL
$banco = "academia";

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica conexão
if ($conn->connect_error) {
  die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Prepara e executa a query
$stmt = $conn->prepare("INSERT INTO vendas (produto, preco_unitario, quantidade, desconto, frete, imposto, total_final) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siddiid", $produto, $preco, $quantidade, $desconto, $frete, $imposto, $totalFinal);

if ($stmt->execute()) {
  echo "<br><br><em>Dados salvos no banco com sucesso!</em>";
} else {
  echo "<br><br><strong>Erro ao salvar no banco: " . $stmt->error . "</strong>";
}

$stmt->close();
$conn->close();

        }
      }
    ?>
    </div>

  </div>
</body>
</html>
