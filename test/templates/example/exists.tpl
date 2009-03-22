<html xmlns:c="urn://Chariot">
    <head>
    </head>
    <body>
        <div c:template="test1" class="main">
            <div c:exists="Bar">
                <span c:value="/Bar/value">exists value</span>
                <span c:value="value">exists value</span>
            </div>
            <div c:notExists="Bar">
                <span c:value="/Baz/value">not exists value</span>
            </div>
        </div>
    </body>
</html>
