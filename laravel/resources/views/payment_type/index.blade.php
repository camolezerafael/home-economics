
<table class="table">
    <thead>
        <tr>

            <th>name</th>

        </tr>
    </thead>
    <tbody>
        <tr v-for="item in list" :key="item">

            <td><span v-text="item.name"></span></td>

        </tr>
    </tbody>
</table>
