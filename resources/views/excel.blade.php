<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traitement Excel - Insee</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #222;
            font-size: 26px;
            margin-bottom: 25px;
        }

        .messages p {
            margin: 8px 0;
        }

        .success {
            color: #28a745;
            font-weight: bold;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }

        .form-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        input[type="file"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .download-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .download-buttons a {
            width: 100%;
            max-width: 300px;
        }

        .download-buttons button {
            width: 100%;
        }

        table.stats {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        table.stats td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table.stats td:first-child {
            text-align: left;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        #stats-table h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Charger et traiter un fichier Excel</h1>

        <div class="messages">
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('excel.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <input type="file" name="file" id="fileInput" required>
                <button type="submit">Charger</button>
            </div>
        </form>

        @if (session('download'))
            <div id="download-buttons" class="download-buttons">
                <a href="{{ session('download') }}">
                    <button>Télécharger le fichier traité</button>
                </a>
                <a href="{{ session('downloadUnmatched') }}">
                    <button>Télécharger les lignes non matchées</button>
                </a>
                @if (session('downloadMatched'))
                    <a href="{{ session('downloadMatched') }}">
                        <button>Télécharger les lignes matchées</button>
                    </a>
                @endif
            </div>
        @endif

        @if (session('stats'))
            <div id="stats-table">
                <h3>Résultat du traitement</h3>
                <table class="stats">
                    <tr>
                        <td>Nombre total de lignes</td>
                        <td><strong>{{ session('stats.total') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Lignes matchées</td>
                        <td><strong>{{ session('stats.matched') }} ({{ session('stats.match_percent') }}%)</strong></td>
                    </tr>
                    <tr>
                        <td>Lignes non matchées</td>
                        <td><strong>{{ session('stats.unmatched') }} ({{ session('stats.unmatch_percent') }}%)</strong></td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('fileInput');
            const downloadButtons = document.getElementById('download-buttons');
            const statsTable = document.getElementById('stats-table');

            if (fileInput) {
                fileInput.addEventListener('change', () => {
                    if (downloadButtons) downloadButtons.style.display = 'none';
                    if (statsTable) statsTable.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
