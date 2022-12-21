
<form action="/from_to" method="POST">

<table class="table">
    <tbody>

        <tr>
            <td class="text-right">name</td>
            <td><select v-model="item.name" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

        <tr>
            <td class="text-right">type</td>
            <td><select v-model="item.type" class="form-control">
    <option v-for="option in options" :key="option" :value="option" v-text="option"></option>
</select></td>
        </tr>

    </tbody>
</table>

</form>
