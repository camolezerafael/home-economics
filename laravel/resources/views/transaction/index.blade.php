
<table class="table">
    <thead>
        <tr>

            <th>user_id</th>

            <th>account_id</th>

            <th>transaction_type</th>

            <th>description</th>

            <th>from_id</th>

            <th>to_id</th>

            <th>category_id</th>

            <th>payment_type_id</th>

            <th>value</th>

            <th>status</th>

            <th>date_due</th>

            <th>date_payment</th>

        </tr>
    </thead>
    <tbody>
        <tr v-for="item in list" :key="item">

            <td><span v-text="item.user_id"></span></td>

            <td><span v-text="item.account_id"></span></td>

            <td><span v-text="item.transaction_type"></span></td>

            <td><span v-text="item.description"></span></td>

            <td><span v-text="item.from_id"></span></td>

            <td><span v-text="item.to_id"></span></td>

            <td><span v-text="item.category_id"></span></td>

            <td><span v-text="item.payment_type_id"></span></td>

            <td><span v-text="item.value"></span></td>

            <td><span v-text="item.status"></span></td>

            <td><span v-text="item.date_due"></span></td>

            <td><span v-text="item.date_payment"></span></td>

        </tr>
    </tbody>
</table>
