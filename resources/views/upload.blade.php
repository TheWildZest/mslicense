<form action="{{ route('data') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div>
        <label for="billingStartDate">Számlázási időszak kezdete</label>
        <input type="date" name="billingStartDate" value="{{ date("Y-m-d") }}">
    </div>

    <div>
        <label for="billingEndDate">Számlázási időszak vége</label>
        <input type="date" name="billingEndDate" value="{{ date("Y-m-d") }}">
    </div>

    <div>
        <label for="euroExchangeRate">Euro árfolyam</label>
        <input type="number" name="euroExchangeRate" step="0.01" value="392">
    </div>

    <div>
        <label for="MBB">Ms 365 Business Basic</label>
        <input type="number" name="MBB" step="0.1" value="5">
    </div>

    <div>
        <label for="MBS">Ms 365 Business Standard</label>
        <input type="number" name="MBS" step="0.1" value="10">
    </div>

    <div>
        <label for="EO">Exchange Online</label>
        <input type="number" name="EO" step="0.1" value="3">
    </div>

    <!--div>
        <label for="chargeType">Számlázás módja</label>
        <select name="chargeType">
            <option value="Cycle fee">Cycle fee</option>
            <option value="Cycle instance prora" selected>Cycle instance prora</option>
        </select>
    </div-->

    <div>
        <input type="file" name="file">
    </div>


    <input type="submit" value="Elküld">
</form>
