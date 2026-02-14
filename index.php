<?php
session_start();
$dir = __DIR__;
$files = array_diff(scandir($dir), array(
        '.',
        '..',
        'index.php',
        '.DS_Store',
        '.git',
        '.github',
        '.well-known',
        '.ftpquota',
        '.idea',
        '__MACOSX',
        'cgi-bin',
        'README.md',
        'assets')
);
$viewMode = $_SESSION['viewMode'] ?? 'grid';

if (isset($_POST['viewMode'])) {
    $_SESSION['viewMode'] = $_POST['viewMode'];
    echo json_encode(['status' => 'success']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Explorer</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚙️</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function setViewMode(mode) {
            fetch('', {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: `viewMode=${mode}`})
                .then(() => { sessionStorage.setItem('viewMode', mode); location.reload(); });
        }

        function toggleFavorite(file) {
            let favs = JSON.parse(localStorage.getItem('favorites') || '[]');
            if (favs.includes(file)) {
                favs = favs.filter(f => f !== file);
            } else {
                favs.push(file);
            }
            localStorage.setItem('favorites', JSON.stringify(favs));
            location.reload();
        }
    </script>
</head>
<body class="bg-gray-100 p-5">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold">PHP File Explorer</h1>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search files..." class="px-3 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300" />
                </div>
                <div>
                    <button onclick="setViewMode('list')" class="px-3 py-1 border rounded <?= $viewMode === 'list' ? 'bg-gray-300' : '' ?>">List</button>
                    <button onclick="setViewMode('grid')" class="px-3 py-1 border rounded <?= $viewMode === 'grid' ? 'bg-gray-300' : '' ?>">Grid</button>
                </div>
            </div>
        </div>
        
        <h2 class="text-lg font-semibold mb-3">Favorites</h2>
        <div id="favorites" class="grid grid-cols-3 gap-4 mb-6"></div>
        
        <h2 class="text-lg font-semibold mb-3">All Files</h2>
        <div id="allFiles" class="<?= $viewMode === 'grid' ? 'grid grid-cols-3 gap-4' : 'flex flex-col space-y-2' ?>">
            <?php foreach ($files as $file): ?>
                <div class="p-3 border rounded-lg bg-gray-50 hover:bg-gray-200 flex justify-between items-center space-x-4">
                    <a href="<?= $file ?>" target="_blank" class="text-blue-500 underline"> <?= $file ?> </a>
                    <button onclick="toggleFavorite('<?= $file ?>')" class="px-2 py-1 bg-yellow-300 rounded">★</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        let favs = JSON.parse(localStorage.getItem('favorites') || '[]');
        let favContainer = document.getElementById('favorites');
        
        // Function to render favorites
        function renderFavorites(filterText = '') {
            favContainer.innerHTML = '';
            const filteredFavs = filterText ? 
                favs.filter(file => file.toLowerCase().includes(filterText.toLowerCase())) : 
                favs;
                
            if (filteredFavs.length) {
                filteredFavs.forEach(file => {
                    let div = document.createElement('div');
                    div.classList.add('p-3', 'border', 'rounded-lg', 'bg-yellow-50', 'hover:bg-yellow-200', 'text-center');
                    div.innerHTML = `<a href="${file}" target="_blank" class="text-blue-500 underline">${file}</a>`;
                    favContainer.appendChild(div);
                });
            } else {
                if (filterText) {
                    favContainer.innerHTML = '<p class="text-gray-500">No matching favorites found.</p>';
                } else {
                    favContainer.innerHTML = '<p class="text-gray-500">No favorites yet.</p>';
                }
            }
        }
        
        // Function to filter all files
        function filterAllFiles(filterText = '') {
            const allFileItems = document.querySelectorAll('#allFiles > div');
            allFileItems.forEach(item => {
                const fileName = item.querySelector('a').textContent.trim();
                if (filterText === '' || fileName.toLowerCase().includes(filterText.toLowerCase())) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Initialize search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchText = this.value.trim();
            renderFavorites(searchText);
            filterAllFiles(searchText);
        });
        
        // Initial render
        renderFavorites();
    </script>
</body>
</html>
