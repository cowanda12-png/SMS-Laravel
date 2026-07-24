<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0b7dda; }
        .back { display: inline-block; margin-top: 10px; color: #2196F3; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Student #{{ $id ?? '1' }}</h1>
        <form>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" value="John Doe">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="john@example.com">
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" value="+1234567890">
            </div>
            <div class="form-group">
                <label>Course:</label>
                <input type="text" value="Computer Science">
            </div>
            <button type="submit" class="btn">Update Student</button>
            <br>
            <a href="{{ route('students.index') }}" class="back">← Back to Students</a>
        </form>
    </div>
</body>
</html>