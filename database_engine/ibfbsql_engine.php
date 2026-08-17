<?php

include_once("engine.php");

class FbConnectionFactory extends ConnectionFactory {
    public function DoCreateConnection($connectionParams) {
        return new FbConnection($connectionParams);
    }

    public function CreateDataReader(IEngConnection $connection, $sql) {
        return new FbDataReader($connection, $sql);
    }

    public function CreateEngCommandImp() {
        return new FbEngCommandImp($this);
    }

}

class FbEngCommandImp extends EngCommandImp {
    public function QuoteIdentifier($identifier) {
        if ($identifier[0] != '"' && $identifier[strlen($identifier) - 1] != '"')
            return '"' . $identifier . '"';
        else
            return $identifier;
    }

    protected function GetBlobFieldValueAsSQL($value) {
        if ($value != null)
            return '?';
    }

    public function ExecuteUpdateCommand($connection, $command) {
        $blobFieldTail = '';
        $blobFieldIndex = 0;
        $fieldValues = $command->GetValues();

        $blobParameterHandles = array();

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name]) && $fieldValues[$field->Name] != null) {
                $blobHandle = ibase_blob_create($connection->GetConnectionHandle());
                if (is_array($fieldValues[$field->Name]))
                    ibase_blob_add($blobHandle, file_get_contents($fieldValues[$field->Name][0]));
                else
                    ibase_blob_add($blobHandle, $fieldValues[$field->Name]);

                $blobParameterHandles[] = ibase_blob_close($blobHandle);
            }
        }

        $queryArgs = array_merge(
            array($connection->GetConnectionHandle(), $command->GetSQL()),
            $blobParameterHandles);
        if (!@call_user_func_array('ibase_query', $queryArgs)) ;
        {
            if ($connection->LastError() != '')
                RaiseError($connection->LastError());
        }
    }

    public function ExecuteInsertCommand($connection, $command) {
        $blobFieldTail = '';
        $blobFieldIndex = 0;
        $fieldValues = $command->GetFieldValues();

        $blobParameterHandles = array();

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name]) && $fieldValues[$field->Name] != null) {
                $blobHandle = ibase_blob_create($connection->GetConnectionHandle());
                if (is_array($fieldValues[$field->Name]))
                    ibase_blob_add($blobHandle, file_get_contents($fieldValues[$field->Name][0]));
                else
                    ibase_blob_add($blobHandle, $fieldValues[$field->Name]);
                $blobParameterHandles[] = ibase_blob_close($blobHandle);
            }
        }

        $queryArgs = array_merge(
            array($connection->GetConnectionHandle(), $command->GetSQL()),
            $blobParameterHandles);
        if (!@call_user_func_array('ibase_query', $queryArgs)) ;
        {
            if ($connection->LastError() != '')
                RaiseError($connection->LastError());
        }
    }

    public function GetCastToCharExpression($value, $fieldInfo) {
        return $value;
    }

    /** @inheritdoc */
    public function getSelectSQLWithLimitation($selectSQL, $limitNumber, $limitOffset, $hasSorting) {
        $limitationExpression = sprintf('FIRST %d SKIP %d', $limitNumber, $limitOffset);
        return preg_replace('/SELECT/', 'SELECT ' . $limitationExpression, $selectSQL, 1);
    }

    protected function getBooleanTrueAsSQL() {
        return 'true';
    }

    protected function getBooleanFalseAsSQL() {
        return 'false';
    }

}

class FbConnection extends EngConnection {
    private $connectionHandle;

    public function IsDriverSupported() {
        return function_exists('ibase_connect');
    }

    protected function DoGetDBMSName() {
        return 'Firebird';

    }

    protected function DoGetDriverExtensionName() {
        return 'ibase';
    }

    protected function DoGetDriverInstallationLink() {
        return 'http://php.net/manual/en/ibase.installation.php';
    }

    protected function DoConnect() {
        if (!function_exists('ibase_connect')) {
            return false;
        }

        if ($this->ConnectionParam('client_encoding') != '')
            $this->connectionHandle = @ibase_connect(

                $this->ConnectionParam('server') .
                    ($this->ConnectionParam('port') == '3050' ? '' : ('/' . $this->ConnectionParam('port'))) .
                    ':' . $this->ConnectionParam('database'),
                $this->ConnectionParam('username'),
                $this->ConnectionParam('password'),
                $this->ConnectionParam('client_encoding')
            );
        else
            $this->connectionHandle = @ibase_connect(
                $this->ConnectionParam('server') .
                    ($this->ConnectionParam('port') == '3050' ? '' : ('/' . $this->ConnectionParam('port'))) .
                    ':' . $this->ConnectionParam('database'),
                $this->ConnectionParam('username'),
                $this->ConnectionParam('password'));
        if (!$this->connectionHandle)
            return false;
        ini_set('ibase.timestampformat', '%Y-%m-%d %H:%M:%S');
        ini_set('ibase.dateformat', '%Y-%m-%d');
        ini_set('ibase.timeformat', '%H:%M:%S');
        return true;
    }

    protected function DoDisconnect() {
        @ibase_close($this->connectionHandle);
    }

    protected function DoCreateDataReader($sql) {
        return new FbDataReader($this, $sql);
    }

    public function GetConnectionHandle() {
        return $this->connectionHandle;
    }

    protected function DoExecSQL($sql) {
        return @ibase_query($this->GetConnectionHandle(), $sql) ? true : false;
    }

    protected function doExecScalarSQL($sql) {
        if ($queryHandle = @ibase_query($this->GetConnectionHandle(), $sql)) {
            $queryResult = @ibase_fetch_row($queryHandle);
            return $queryResult[0];
        }
        return false;
    }

    public function DoLastError() {
        return @ibase_errmsg();
    }

    public function commitTransaction() {
        $this->ExecSQL("COMMIT");
    }
}

class FbDataReader extends EngDataReader {
    private $queryResult;
    private $lastFetchedRow;
    /** @var FbConnection */
    private $fbConnection;

    public function __construct($connection, $sql) {
        parent::__construct($connection, $sql);
        $this->queryResult = null;
        $this->fbConnection = $connection;
    }

    protected function FetchField() {
        echo "not supported";
    }

    protected function FetchFields() {
        for ($i = 0; $i < ibase_num_fields($this->queryResult); $i++) {
            $fieldInfo = ibase_field_info($this->queryResult, $i);
            if (isset($fieldInfo['alias']) && $fieldInfo['alias'] != '')
                $this->AddField($fieldInfo['alias']);
            else
                $this->AddField($fieldInfo['name']);
        }
    }

    protected function DoOpen() {
        $this->queryResult = @ibase_query($this->fbConnection->GetConnectionHandle(), $this->GetSQL());
        if ($this->queryResult)
            return true;
        return false;
    }

    public function Opened() {
        return $this->queryResult ? true : false;
    }

    public function Seek($ARowIndex) {
    }

    public function Next() {
        $this->lastFetchedRow = @ibase_fetch_assoc($this->queryResult, IBASE_FETCH_BLOBS);
        return $this->lastFetchedRow ? true : false;
    }

    public function GetFieldValueByName($fieldName) {
        if ($this->lastFetchedRow) {
            return $this->GetActualFieldValue($fieldName, $this->lastFetchedRow[$fieldName]);
        } else {
            return null;
        }
    }
}
