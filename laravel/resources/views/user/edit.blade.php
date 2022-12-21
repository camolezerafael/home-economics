
<form action="/user" method="POST">

<table class="table">
    <tbody>

        <tr>
            <td class="text-right">name</td>
            <td><input v-model="item.name" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">email</td>
            <td><input v-model="item.email" class="form-control" type="text" /></td>
        </tr>

        <tr>
            <td class="text-right">password</td>
            <td><input v-model="item.password" class="form-control" type="text" /></td>
        </tr>

    </tbody>
</table>

</form>
