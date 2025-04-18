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
            padding: 40px 60px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 1140px;
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

        table.stats {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        table.stats td {
            padding: 12px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        table.stats td:first-child {
            text-align: left;
            font-weight: 600;
            background-color: #f8f9fa;
            width: 45%;
        }

        .download-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .download-group a button {
            width: 100%;
        }

        #stats-table h3 {
            margin-bottom: 10px;
        }

        table.stats {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 16px;
        }
        
        table.stats td {
            padding: 16px 20px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        table.stats td:first-child {
            text-align: left;
            font-weight: 600;
            background-color: #f8f9fa;
            width: 35%;
        }
        
        table.stats td:nth-child(2) {
            width: 20%;
            text-align: center;
        }
        
        table.stats td:last-child {
            width: 45%;
            text-align: center;
            background-color: #fefefe;
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

        @if (session('stats'))
            <div id="stats-table">
                <h3>Résultat du traitement</h3>
                <table class="stats">
                    <tr>
                        <td>Nombre total de lignes</td>
                        <td><strong>{{ session('stats.total') }}</strong></td>
                        <td>
                            <a href="{{ session('download') }}">
                                <button>Télécharger</button>
                            </a>
                            <br/><small>Full (fichier traité)</small>

                        </td>
                    </tr>
                    <tr>
                        <td>Lignes matchées <br/><small>(colorié en jaune)</small></td>
                        <td><strong>{{ session('stats.matched') }} ({{ session('stats.match_percent') }}%)</strong></td>
                        <td>
                            @if (session('downloadMatched'))
                                <a href="{{ session('downloadMatched') }}">
                                    <button>Télécharger</button>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Lignes non matchées</td>
                        <td><strong>{{ session('stats.unmatched') }} ({{ session('stats.unmatch_percent') }}%)</strong>
                        </td>
                        <td>
                            <a href="{{ session('downloadUnmatched') }}">
                                <button>Télécharger</button>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const statsTable = document.getElementById('stats-table');

            if (fileInput) {
                fileInput.addEventListener('change', () => {
                    if (statsTable) statsTable.style.display = 'none';
                });
            }
        });
    </script>
</body>

</html>
