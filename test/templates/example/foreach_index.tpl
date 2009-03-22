<html xmlns:c="urn://Chariot">
    <head>
    </head>
    <body>
        <div c:template="test1" class="main">
            <table>
                <tr c:foreach="Foo">
                    <td><span c:value="_index">index</span>:<span c:value="name">name</span>:<span c:value="value">value</span></td>
                </tr>
            </table>
        </div>
    </body>
</html>
