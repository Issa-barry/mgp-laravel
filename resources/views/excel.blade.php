<!DOCTYPE html>
<html>
<head>
    <title>Traitement Excel - Insee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
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
            flex: 1;
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
                <input type="file" name="file" required>
                <button type="submit">Charger</button>
            </div>
        </form>

        @if (session('download'))
            <div class="download-buttons">
                <a href="{{ session('download') }}">
                    <button>Télécharger le fichier traité</button>
                </a>
                <a href="{{ session('downloadUnmatched') }}">
                    <button>Télécharger uniquement les lignes non matchées</button>
                </a>
            </div>
        @endif
    </div>
</body>
</html>
