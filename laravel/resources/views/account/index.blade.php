
<table class="table">
    <thead>
        <tr>

            <th>user_id</th>

            <th>name</th>

            <th>description</th>

            <th>initial_balance</th>

            <th>decimal_precision</th>

            <th>type_id</th>

        </tr>
    </thead>
    <tbody>
        <tr v-for="item in list" :key="item">

            <td><span v-text="item.user_id"></span></td>

            <td><span v-text="item.name"></span></td>

            <td><span v-text="item.description"></span></td>

            <td><span v-text="item.initial_balance"></span></td>

            <td><span v-text="item.decimal_precision"></span></td>

            <td><span v-text="item.type_id"></span></td>

        </tr>
    </tbody>
</table>
