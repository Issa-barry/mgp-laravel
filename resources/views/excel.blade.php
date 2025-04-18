<!DOCTYPE html>
<html>
<head>
    <title>Traitement Excel - Insee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 700px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .messages {
            margin-bottom: 20px;
        }

        .messages p {
            margin: 5px 0;
        }

        .form-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        input[type="file"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
        }

        .download-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        table.stats {
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.stats td {
            padding: 8px 15px;
            border: 1px solid #ccc;
        }

        table.stats tr:nth-child(1) {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Charger et traiter un fichier Excel</h1>

        <div class="messages">
            @if ($errors->any())
                <div style="color:red;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <p style="color:green;">{{ session('success') }}</p>
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
                    <button id="btn-processed">Télécharger le fichier traité</button>
                </a>
                <a href="{{ session('downloadUnmatched') }}">
                    <button id="btn-unmatched">Télécharger les lignes non matchées</button>
                </a>
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
                    if (downloadButtons) {
                        downloadButtons.style.display = 'none';
                    }

                    if (statsTable) {
                        statsTable.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
