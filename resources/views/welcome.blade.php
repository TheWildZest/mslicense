<form action="{{ route('processCSV') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="file">
    <input type="number" name="euroExchangeRate" step="0.01" value="392">

    <input type="submit" value="Elküld">
</form>
