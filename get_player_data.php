<?php
header('Content-Type: application/json');

if (isset($_GET['nom'])) {
    $nom = htmlspecialchars($_GET['nom']);
    
    try {
        $db = new PDO("mysql:host=localhost;dbname=lep-fbf", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("SELECT id_joueur, prenom, datnaissance, ville FROM joueur WHERE nom = :nom");
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($player) {
            echo json_encode($player);
        } else {
            echo json_encode(['error' => 'Aucun joueur trouvé']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur de connexion : ' . htmlspecialchars($e->getMessage())]);
    }
} else {
    echo json_encode(['error' => 'Paramètre nom manquant']);
}
?>

