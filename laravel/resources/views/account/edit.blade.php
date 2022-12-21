
<form action="/account" method="POST">

<table class="table">
    <tbody>

        <tr>
            <td class="text-right">name</td>
            <td><input v-model="item.name" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">description</td>
            <td><textarea v-model="item.description" class="form-control"></textarea></td>
        </tr>

        <tr>
            <td class="text-right">initial_balance</td>
            <td><input v-model="item.initial_balance" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">decimal_precision</td>
            <td><input v-model="item.decimal_precision" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">type_id</td>
            <td><select v-model="item.type_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

    </tbody>
</table>

</form>
