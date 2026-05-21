<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../inc/conexao.php';


$usuario_id = $_GET["usuario_id"] ?? '';
if ($usuario_id == '') {
    echo "<script>alert('Usuário não informado.'); window.location.href='usuario_listar.php';</script>";
    exit;
}

$sql = "SELECT usuario_id, nome, login FROM tbUsuarios WHERE usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($idBanco, $nomeBanco, $loginBanco);

if (!$stmt->fetch()) {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='usuario_listar.php';</script>";
    exit;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../styles/employees.css" />
    <link rel="stylesheet" href="../styles/sideBar.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function salvarUsuario() {
            let vId = document.getElementById("id").value;
            let vNome = document.getElementById("nome").value;
            let vLogin = document.getElementById("login").value;
            let vSenha = document.getElementById("senha").value;

            if (vId.trim() === "" || vNome.trim() === "" || vLogin.trim() === "") {
                alert("Preencha todos os campos obrigatórios.");
                return;
            }

            $.ajax({
                url: '../scripts/acao_editar.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    pId: vId,
                    pNome: vNome,
                    pLogin: vLogin,
                    pSenha: vSenha
                },
                success: function (data) {
                    if (data.status === "success") {
                        alert(data.message);
                        window.location.href = "usuario_listar.php";
                    } else {
                        alert(data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("Erro na requisição: " + textStatus + " - " + errorThrown);
                }
            });
        }
    </script>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-top"><div class="logo"><div class="logo-text"><div class="app-title">PerformanceHub</div></div></div></div>
        <nav class="nav"><a href="usuario_listar.php" class="nav-item active">Voltar para Usuários</a></nav>
    </aside>

    <main class="content">
        <header class="header">
            <div>
                <h1 class="page-title">Editar Usuário</h1>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-secondary" onclick="window.location.href='usuario_listar.php'">Cancelar</button>
                <button type="button" class="btn-primary" onclick="salvarUsuario()">Gravar Alterações</button>
            </div>
        </header>

        <div style="background: #fff; padding: 30px; border-radius: 8px; max-width: 600px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <form id="formUsuario" onsubmit="return false;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">ID</label>
                    <input type="text" id="id" value="<?php echo htmlspecialchars($idBanco ?? ''); ?>" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; background: #f1f5f9; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nome Completo</label>
                    <input type="text" id="nome" value="<?php echo htmlspecialchars($nomeBanco ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">E-mail (Login)</label>
                    <input type="text" id="login" value="<?php echo htmlspecialchars($loginBanco ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nova Senha</label>
                    <input type="password" id="senha" placeholder="••••••••" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" autocomplete="new-password">
                    <small style="color: #64748b; font-size: 13px;">Deixe em branco para não alterar a senha atual.</small>
                </div>
            </form>
        </div>
    </main>
</body>
</html>