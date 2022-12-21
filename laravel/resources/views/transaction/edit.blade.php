
<form action="/transaction" method="POST">

<table class="table">
    <tbody>

        <tr>
            <td class="text-right">account_id</td>
            <td><select v-model="item.account_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">transaction_type</td>
            <td><select v-model="item.transaction_type" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">description</td>
            <td><input v-model="item.description" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">from_id</td>
            <td><select v-model="item.from_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">to_id</td>
            <td><select v-model="item.to_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">category_id</td>
            <td><select v-model="item.category_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">payment_type_id</td>
            <td><select v-model="item.payment_type_id" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">value</td>
            <td><input v-model="item.value" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">status</td>
            <td><label v-for="option in options" :key="option">
    <input v-model="item.status" :value="option" class="form-control" type="checkbox" />
    <span v-text="option"></span>
</label></td>
        </tr>

        <tr>
            <td class="text-right">date_due</td>
            <td><input v-model="item.date_due" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">date_payment</td>
            <td><input v-model="item.date_payment" class="form-control" type="text" /></td>
        </tr>

    </tbody>
</table>

</form>
