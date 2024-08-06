<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "examen";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Initialisation des messages
$error_message = "";

// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mot_de_passe = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Vérifie si les champs sont remplis
    if (empty($email) || empty($mot_de_passe)) {
        $error_message = "Tous les champs sont requis.";
    } else {
        // Prépare et exécute la requête SQL
        $sql = "SELECT id_conn, password FROM connexion WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Vérifie si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($mot_de_passe, $user['password'])) {
                // Connexion réussie
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id_conn'];
                header("Location: candidat.php"); // Redirection après connexion
                exit();
            } else {
                $error_message = "Email ou mot de passe incorrect.";
            }

            $stmt->close();
        } else {
            $error_message = "Erreur de préparation de la requête : " . $conn->error;
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Connexion</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 110vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 80%;
        }

        h1 {
            margin-top: 50px; /* Ajustez cette valeur pour pousser le titre vers le bas */
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
            text-align: center;
        }

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #004494;
            transform: translateY(1px);
        }

        .signup-link {
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
        }

        .signup-link a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CYA SERVICES</h1>
        <!-- Affiche les messages d'erreur -->
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="connexion.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <div class="signup-link">
            <p>Pas encore de compte? <a href="creation.php">S'inscrire</a></p>
        </div>
        <div class="signup-link">
        <a Mot de passe oublié?  href="mot_de_passe_reinitialisation.php">Mot de passe oublié?</a></p>

    </div>
</body>
</html>
