<?php
// Asegúrate de reemplazar 'TU_CLAVE_DE_ACCESO_UNSPLASH' con tu clave real de Unsplash
$unsplash_access_key = 'ZV_1HOexry8Uge1Mg1uaEct-P1wfqmyJHea5n2JZeEw';

function searchImages($query, $access_key) {
    $url = 'https://api.unsplash.com/search/photos?query=' . urlencode($query) . '&per_page=12';
    $options = [
        'http' => [
            'header' => "Authorization: Client-ID $access_key\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        return [];
    }
    
    $data = json_decode($response, true);
    return $data['results'] ?? [];
}

$images = [];
$query = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);
    if (!empty($query)) {
        $images = searchImages($query, $unsplash_access_key);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Imágenes Unsplash</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0f0f0;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .image-item {
            background-color: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .image-item:hover {
            transform: scale(1.05);
        }
        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .image-item p {
            padding: 10px;
            margin: 0;
            text-align: center;
            font-size: 14px;
            color: #333;
        }
        .image-item a {
            display: block;
            padding: 10px;
            text-align: center;
            background-color: #f8f8f8;
            color: #333;
            text-decoration: none;
            font-size: 12px;
        }
        .image-item a:hover {
            background-color: #e8e8e8;
        }
    </style>
</head>
<body>
    <h1>Buscador de Imágenes Unsplash</h1>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="query" placeholder="Buscar imágenes..." value="<?php echo $query; ?>" required>
        <input type="submit" value="Buscar">
    </form>

    <?php if (!empty($images)): ?>
        <div class="image-grid">
            <?php foreach ($images as $image): ?>
                <div class="image-item">
                    <img src="<?php echo htmlspecialchars($image['urls']['small']); ?>" alt="<?php echo htmlspecialchars($image['alt_description']); ?>">
                    <p><?php echo htmlspecialchars($image['alt_description'] ?? 'Sin descripción'); ?></p>
                    <a href="<?php echo htmlspecialchars($image['links']['html']); ?>" target="_blank">Ver en Unsplash</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($query)): ?>
        <p>No se encontraron imágenes para "<?php echo $query; ?>"</p>
    <?php endif; ?>
</body>
</html>