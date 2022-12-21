
<form action="/category" method="POST">

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

    </tbody>
</table>

</form>
