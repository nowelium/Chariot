<html xmlns:c="urn://Chariot">
    <head>
    </head>
    <body>
        <div c:template="test1" class="main">
            <div c:exists="Hoge">
                <span>Hoge is exist</span>
            </div>
            <div c:exists="Foo">
                <span>Foo is exist</span>
            </div>
        </div>
    </body>
</html>
