<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>رفع صور للمساحة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .gallery {
            margin-top: 30px;
        }

        .gallery h3 {
            margin-bottom: 10px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }

        .gallery-grid img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="{{ url()->previous() }}" class="back-button">⬅️ رجوع</a>

        <h2>رفع صور لمساحة: {{ $workspace->name }}</h2>

        {{-- إشعار النجاح --}}
        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- إشعار الأخطاء --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- النموذج --}}
        <form action="{{ route('admin.upload-images.store', $workspace->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <label for="images">اختر الصور:</label>
            <input type="file" name="images[]" id="images" multiple accept="image/*" required>

            <button type="submit">رفع الصور</button>
        </form>

        {{-- معرض الصور --}}
        @if ($workspace->images && $workspace->images->count())
            <div class="gallery">
                <h3>معرض الصور الحالي:</h3>
                <div class="gallery-grid">
                    @foreach ($workspace->images as $image)
                        <div class="image-container">
                            <img src="{{ asset($image->image) }}" alt="صورة">
                            <form action="{{ route('admin.workspace-images.destroy', $image->id) }}" method="POST"
                                class="delete-form" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">حذف</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
        function confirmDelete() {
            return confirm("هل أنت متأكد من أنك تريد حذف هذه الصورة؟");
        }
    </script>
</body>

</html>
