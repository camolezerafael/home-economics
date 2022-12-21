
<table class="table">
    <thead>
        <tr>

            <th>name</th>

            <th>created_at</th>

        </tr>
    </thead>
    <tbody>
        <tr v-for="item in list" :key="item">

            <td><span v-text="item.name"></span></td>

            <td><span v-text="item.created_at"></span></td>

        </tr>
    </tbody>
</table>
