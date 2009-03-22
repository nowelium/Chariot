<html xmlns:c="urn://Chariot">
    <head>
    </head>
    <body>
        <div c:template="test1" class="main">
            <ul c:foreach="Hoge">
                <li><span c:value="name">name</span><span c:value="entry">entry</span></li>
            </ul>
            <table>
                <tr c:foreach="Foo" class="dummy">
                    <td><span c:value="_index">index</span>:<span c:value="name">name</span>:<span c:value="value">value</span></td>
                </tr>
            </table>
            <div c:if="Bar != null">
                <span c:value="Bar.value">value</span>
                <div>
                    <div>
                        <div>
                            <div c:foreach="Baz">
                                <span c:value="_index" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div c:foreach="Baz">
                <span c:value="hello">hello world</span>
            </div>
            
            <c:symfony target="include_component">
                <c:param name="hoge" value="Bar" />
                <c:param name="foo" value="Bar.key1" />
            </c:symfony>
        </div>
    </body>
</html>
