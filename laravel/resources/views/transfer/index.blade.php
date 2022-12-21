
<table class="table">
    <thead>
        <tr>

            <th>from_account_id</th>

            <th>to_account_id</th>

            <th>value</th>

        </tr>
    </thead>
    <tbody>
        <tr v-for="item in list" :key="item">

            <td><span v-text="item.from_account_id"></span></td>

            <td><span v-text="item.to_account_id"></span></td>

            <td><span v-text="item.value"></span></td>

        </tr>
    </tbody>
</table>
