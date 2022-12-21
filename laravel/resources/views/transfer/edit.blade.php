
<form action="/transfer" method="POST">

<table class="table">
    <tbody>

        <tr>
            <td class="text-right">from_account_id</td>
            <td><select v-model="item.from_account_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">to_account_id</td>
            <td><select v-model="item.to_account_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">value</td>
            <td><input v-model="item.value" class="form-control" type="text" /></td>
        </tr>

    </tbody>
</table>

</form>
