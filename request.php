<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/ibfbsql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'UTF8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    class REQUEST_FROM_SMOModalViewPage extends ViewBasedPage
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $this->dataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for SMO_ID field
            //
            $column = new NumberViewColumn('SMO_ID', 'SMO_ID', 'SMO ID', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('SMO_NAME', 'SMO_NAME', 'SMO NAME', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
    
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            
            
        }
    
        static public function getHandlerName() {
            return get_class() . '_modal_view';
        }
    
        public function GetModalGridViewHandler() {
            return self::getHandlerName();
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    }
    
    // OnBeforePageExecute event handler
    
    
    
    class REQUESTPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Запросы мед.документации');
            $this->SetMenuLabel('Запросы мед.документации');
    
            $this->dataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"REQUEST"');
            $this->dataset->addFields(
                array(
                    new IntegerField('ID_REQ', true, true, true),
                    new DateField('RECEIVE_DATE'),
                    new IntegerField('FROM_SMO'),
                    new StringField('REQUEST_NUMBER'),
                    new DateField('DUE_DATE'),
                    new BlobField('RECEIVED_FILES'),
                    new IntegerField('SEND'),
                    new IntegerField('DAYS_LEFT'),
                    new StringField('FILE_TYPE')
                )
            );
            $this->dataset->AddLookupField('FROM_SMO', 'SMO', new IntegerField('SMO_ID'), new StringField('SMO_NAME', false, false, false, false, 'LA1', 'LT1'), 'LT1');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'ID_REQ', 'ID_REQ', '№'),
                new FilterColumn($this->dataset, 'RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса'),
                new FilterColumn($this->dataset, 'FROM_SMO', 'LA1', 'От кого получен запрос'),
                new FilterColumn($this->dataset, 'REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса'),
                new FilterColumn($this->dataset, 'DUE_DATE', 'DUE_DATE', 'До какого числа предоставить'),
                new FilterColumn($this->dataset, 'RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса'),
                new FilterColumn($this->dataset, 'SEND', 'SEND', 'Отметка об отправке'),
                new FilterColumn($this->dataset, 'DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней'),
                new FilterColumn($this->dataset, 'FILE_TYPE', 'FILE_TYPE', 'FILE TYPE')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['ID_REQ'])
                ->addColumn($columns['RECEIVE_DATE'])
                ->addColumn($columns['FROM_SMO'])
                ->addColumn($columns['REQUEST_NUMBER'])
                ->addColumn($columns['DUE_DATE'])
                ->addColumn($columns['RECEIVED_FILES'])
                ->addColumn($columns['SEND'])
                ->addColumn($columns['DAYS_LEFT']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('RECEIVE_DATE')
                ->setOptionsFor('FROM_SMO')
                ->setOptionsFor('DUE_DATE')
                ->setOptionsFor('RECEIVED_FILES');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('id_req_edit');
            
            $filterBuilder->addColumn(
                $columns['ID_REQ'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('receive_date_edit', false, 'd.m.Y');
            
            $filterBuilder->addColumn(
                $columns['RECEIVE_DATE'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DynamicCombobox('from_smo_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_REQUEST_FROM_SMO_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('FROM_SMO', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_REQUEST_FROM_SMO_search');
            
            $text_editor = new TextEdit('FROM_SMO');
            
            $filterBuilder->addColumn(
                $columns['FROM_SMO'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $text_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $text_editor,
                    FilterConditionOperator::BEGINS_WITH => $text_editor,
                    FilterConditionOperator::ENDS_WITH => $text_editor,
                    FilterConditionOperator::IS_LIKE => $text_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $text_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('request_number_edit');
            $main_editor->SetMaxLength(15);
            
            $filterBuilder->addColumn(
                $columns['REQUEST_NUMBER'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('due_date_edit', false, 'd.m.Y');
            
            $filterBuilder->addColumn(
                $columns['DUE_DATE'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('RECEIVED_FILES');
            
            $filterBuilder->addColumn(
                $columns['RECEIVED_FILES'],
                array(
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new ComboBox('SEND');
            $main_editor->SetAllowNullValue(false);
            $main_editor->addChoice(true, $this->GetLocalizerCaptions()->GetMessageString('True'));
            $main_editor->addChoice(false, $this->GetLocalizerCaptions()->GetMessageString('False'));
            
            $filterBuilder->addColumn(
                $columns['SEND'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('days_left_edit');
            
            $filterBuilder->addColumn(
                $columns['DAYS_LEFT'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant()) {
            
                $operation = new AjaxOperation(OPERATION_VIEW,
                    $this->GetLocalizerCaptions()->GetMessageString('View'),
                    $this->GetLocalizerCaptions()->GetMessageString('View'), $this->dataset,
                    $this->GetModalGridViewHandler(), $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new AjaxOperation(OPERATION_EDIT,
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'),
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'), $this->dataset,
                    $this->GetGridEditHandler(), $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for ID_REQ field
            //
            $column = new NumberViewColumn('ID_REQ', 'ID_REQ', '№', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
            //
            // View column for RECEIVE_DATE field
            //
            $column = new DateTimeViewColumn('RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('Дата получения запроса');
            $grid->AddViewColumn($column);
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('FROM_SMO', 'LA1', 'От кого получен запрос', $this->dataset);
            $column->SetOrderable(true);
            $column->setLookupRecordModalViewHandlerName(REQUEST_FROM_SMOModalViewPage::getHandlerName());
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('От кого');
            $grid->AddViewColumn($column);
            //
            // View column for REQUEST_NUMBER field
            //
            $column = new TextViewColumn('REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('Номер запроса');
            $grid->AddViewColumn($column);
            //
            // View column for DUE_DATE field
            //
            $column = new DateTimeViewColumn('DUE_DATE', 'DUE_DATE', 'До какого числа предоставить', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('До какого числа нужен ответ');
            $grid->AddViewColumn($column);
            //
            // View column for RECEIVED_FILES field
            //
            $column = new DownloadDataColumn('RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('Полученные файлы');
            $grid->AddViewColumn($column);
            //
            // View column for SEND field
            //
            $column = new CheckboxViewColumn('SEND', 'SEND', 'Отметка об отправке', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->setDescription('Отметка об отправке документации');
            $grid->AddViewColumn($column);
            //
            // View column for DAYS_LEFT field
            //
            $column = new NumberViewColumn('DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for ID_REQ field
            //
            $column = new NumberViewColumn('ID_REQ', 'ID_REQ', '№', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for RECEIVE_DATE field
            //
            $column = new DateTimeViewColumn('RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('FROM_SMO', 'LA1', 'От кого получен запрос', $this->dataset);
            $column->SetOrderable(true);
            $column->setLookupRecordModalViewHandlerName(REQUEST_FROM_SMOModalViewPage::getHandlerName());
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for REQUEST_NUMBER field
            //
            $column = new TextViewColumn('REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for DUE_DATE field
            //
            $column = new DateTimeViewColumn('DUE_DATE', 'DUE_DATE', 'До какого числа предоставить', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for RECEIVED_FILES field
            //
            $column = new DownloadDataColumn('RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for SEND field
            //
            $column = new CheckboxViewColumn('SEND', 'SEND', 'Отметка об отправке', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for DAYS_LEFT field
            //
            $column = new NumberViewColumn('DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for FILE_TYPE field
            //
            $column = new TextViewColumn('FILE_TYPE', 'FILE_TYPE', 'FILE TYPE', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for RECEIVE_DATE field
            //
            $editor = new DateTimeEdit('receive_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('Дата получения запроса', 'RECEIVE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for FROM_SMO field
            //
            $editor = new DynamicCombobox('from_smo_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $editColumn = new DynamicLookupEditColumn('От кого получен запрос', 'FROM_SMO', 'LA1', 'edit_REQUEST_FROM_SMO_search', $editor, $this->dataset, $lookupDataset, 'SMO_ID', 'SMO_NAME', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for REQUEST_NUMBER field
            //
            $editor = new TextEdit('request_number_edit');
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Номер запроса', 'REQUEST_NUMBER', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for DUE_DATE field
            //
            $editor = new DateTimeEdit('due_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('До какого числа предоставить', 'DUE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for RECEIVED_FILES field
            //
            $editor = new ImageUploader('received_files_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('Файл запроса', 'RECEIVED_FILES', $editor, $this->dataset, false, false, 'REQUEST_RECEIVED_FILES_handler_edit');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for SEND field
            //
            $editor = new CheckBox('send_edit');
            $editColumn = new CustomEditColumn('Отметка об отправке', 'SEND', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for FILE_TYPE field
            //
            $editor = new ComboBox('file_type_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('docx', 'docx');
            $editor->addChoice('xlsx', 'xlsx');
            $editor->addChoice('pdf', 'pdf');
            $editor->addChoice('xls', 'xls');
            $editor->addChoice('doc', 'doc');
            $editor->addChoice('rtf', 'rtf');
            $editColumn = new CustomEditColumn('FILE TYPE', 'FILE_TYPE', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for RECEIVE_DATE field
            //
            $editor = new DateTimeEdit('receive_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('Дата получения запроса', 'RECEIVE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for FROM_SMO field
            //
            $editor = new DynamicCombobox('from_smo_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $editColumn = new DynamicLookupEditColumn('От кого получен запрос', 'FROM_SMO', 'LA1', 'multi_edit_REQUEST_FROM_SMO_search', $editor, $this->dataset, $lookupDataset, 'SMO_ID', 'SMO_NAME', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for REQUEST_NUMBER field
            //
            $editor = new TextEdit('request_number_edit');
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Номер запроса', 'REQUEST_NUMBER', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for DUE_DATE field
            //
            $editor = new DateTimeEdit('due_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('До какого числа предоставить', 'DUE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for RECEIVED_FILES field
            //
            $editor = new ImageUploader('received_files_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('Файл запроса', 'RECEIVED_FILES', $editor, $this->dataset, false, false, 'REQUEST_RECEIVED_FILES_handler_multi_edit');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for SEND field
            //
            $editor = new CheckBox('send_edit');
            $editColumn = new CustomEditColumn('Отметка об отправке', 'SEND', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddToggleEditColumns(Grid $grid)
        {
    
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for RECEIVE_DATE field
            //
            $editor = new DateTimeEdit('receive_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('Дата получения запроса', 'RECEIVE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetInsertDefaultValue('%CURRENT_DATE%');
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for FROM_SMO field
            //
            $editor = new DynamicCombobox('from_smo_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $editColumn = new DynamicLookupEditColumn('От кого получен запрос', 'FROM_SMO', 'LA1', 'insert_REQUEST_FROM_SMO_search', $editor, $this->dataset, $lookupDataset, 'SMO_ID', 'SMO_NAME', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for REQUEST_NUMBER field
            //
            $editor = new TextEdit('request_number_edit');
            $editor->SetMaxLength(15);
            $editColumn = new CustomEditColumn('Номер запроса', 'REQUEST_NUMBER', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for DUE_DATE field
            //
            $editor = new DateTimeEdit('due_date_edit', false, 'd.m.Y');
            $editColumn = new CustomEditColumn('До какого числа предоставить', 'DUE_DATE', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetInsertDefaultValue('%RECEIVE_DATE%');
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for RECEIVED_FILES field
            //
            $editor = new ImageUploader('received_files_edit');
            $editor->SetShowImage(false);
            $editColumn = new FileUploadingColumn('Файл запроса', 'RECEIVED_FILES', $editor, $this->dataset, false, false, 'REQUEST_RECEIVED_FILES_handler_insert');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for SEND field
            //
            $editor = new CheckBox('send_edit');
            $editColumn = new CustomEditColumn('Отметка об отправке', 'SEND', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for FILE_TYPE field
            //
            $editor = new ComboBox('file_type_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('docx', 'docx');
            $editor->addChoice('xlsx', 'xlsx');
            $editor->addChoice('pdf', 'pdf');
            $editor->addChoice('xls', 'xls');
            $editor->addChoice('doc', 'doc');
            $editor->addChoice('rtf', 'rtf');
            $editColumn = new CustomEditColumn('FILE TYPE', 'FILE_TYPE', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for ID_REQ field
            //
            $column = new NumberViewColumn('ID_REQ', 'ID_REQ', '№', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for RECEIVE_DATE field
            //
            $column = new DateTimeViewColumn('RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddPrintColumn($column);
            
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('FROM_SMO', 'LA1', 'От кого получен запрос', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for REQUEST_NUMBER field
            //
            $column = new TextViewColumn('REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for DUE_DATE field
            //
            $column = new DateTimeViewColumn('DUE_DATE', 'DUE_DATE', 'До какого числа предоставить', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddPrintColumn($column);
            
            //
            // View column for RECEIVED_FILES field
            //
            $column = new DownloadDataColumn('RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for SEND field
            //
            $column = new CheckboxViewColumn('SEND', 'SEND', 'Отметка об отправке', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddPrintColumn($column);
            
            //
            // View column for DAYS_LEFT field
            //
            $column = new NumberViewColumn('DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for ID_REQ field
            //
            $column = new NumberViewColumn('ID_REQ', 'ID_REQ', '№', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for RECEIVE_DATE field
            //
            $column = new DateTimeViewColumn('RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddExportColumn($column);
            
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('FROM_SMO', 'LA1', 'От кого получен запрос', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for REQUEST_NUMBER field
            //
            $column = new TextViewColumn('REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for DUE_DATE field
            //
            $column = new DateTimeViewColumn('DUE_DATE', 'DUE_DATE', 'До какого числа предоставить', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddExportColumn($column);
            
            //
            // View column for RECEIVED_FILES field
            //
            $column = new DownloadDataColumn('RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for SEND field
            //
            $column = new CheckboxViewColumn('SEND', 'SEND', 'Отметка об отправке', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddExportColumn($column);
            
            //
            // View column for DAYS_LEFT field
            //
            $column = new NumberViewColumn('DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for RECEIVE_DATE field
            //
            $column = new DateTimeViewColumn('RECEIVE_DATE', 'RECEIVE_DATE', 'Дата получения запроса', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddCompareColumn($column);
            
            //
            // View column for SMO_NAME field
            //
            $column = new TextViewColumn('FROM_SMO', 'LA1', 'От кого получен запрос', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for REQUEST_NUMBER field
            //
            $column = new TextViewColumn('REQUEST_NUMBER', 'REQUEST_NUMBER', 'Номер запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for DUE_DATE field
            //
            $column = new DateTimeViewColumn('DUE_DATE', 'DUE_DATE', 'До какого числа предоставить', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y');
            $grid->AddCompareColumn($column);
            
            //
            // View column for RECEIVED_FILES field
            //
            $column = new DownloadDataColumn('RECEIVED_FILES', 'RECEIVED_FILES', 'Файл запроса', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for SEND field
            //
            $column = new CheckboxViewColumn('SEND', 'SEND', 'Отметка об отправке', $this->dataset);
            $column->SetOrderable(true);
            $column->setDisplayValues('<span class="pg-row-checkbox checked"></span>', '<span class="pg-row-checkbox"></span>');
            $grid->AddCompareColumn($column);
            
            //
            // View column for DAYS_LEFT field
            //
            $column = new NumberViewColumn('DAYS_LEFT', 'DAYS_LEFT', 'Осталось дней', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        
        public function GetEnableModalGridInsert() { return true; }
        public function GetEnableModalSingleRecordView() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(true);
            $result->setAllowCompare(true);
            $this->AddCompareHeaderColumns($result);
            $this->AddCompareColumns($result);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && true);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            $result->setReloadPageAfterAjaxOperation(true);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddToggleEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setAllowedActions(array('view', 'insert', 'copy', 'edit', 'multi-edit', 'multi-delete'));
            $this->setPrintListAvailable(true);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(true);
            $this->setAllowPrintSelectedRecords(true);
            $this->setExportListAvailable(array('pdf', 'excel', 'word'));
            $this->setExportSelectedRecordsAvailable(array('pdf', 'excel', 'word'));
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array('pdf', 'excel', 'word'));
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            $handler = new DownloadHTTPHandler($this->dataset, 'RECEIVED_FILES', 'RECEIVED_FILES_handler', '', '%REQUEST_NUMBER%.%FILE_TYPE%', true);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new DownloadHTTPHandler($this->dataset, 'RECEIVED_FILES', 'RECEIVED_FILES_handler', '', '%REQUEST_NUMBER%.%FILE_TYPE%', true);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new DownloadHTTPHandler($this->dataset, 'RECEIVED_FILES', 'RECEIVED_FILES_handler', '', '%REQUEST_NUMBER%.%FILE_TYPE%', true);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, 'insert_REQUEST_FROM_SMO_search', 'SMO_ID', 'SMO_NAME', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'RECEIVED_FILES', 'REQUEST_RECEIVED_FILES_handler_insert', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, 'filter_builder_REQUEST_FROM_SMO_search', 'SMO_ID', 'SMO_NAME', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new DownloadHTTPHandler($this->dataset, 'RECEIVED_FILES', 'RECEIVED_FILES_handler', '', '%REQUEST_NUMBER%.%FILE_TYPE%', true);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, 'edit_REQUEST_FROM_SMO_search', 'SMO_ID', 'SMO_NAME', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'RECEIVED_FILES', 'REQUEST_RECEIVED_FILES_handler_edit', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                FbConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '"SMO"');
            $lookupDataset->addFields(
                array(
                    new IntegerField('SMO_ID', true, true, true),
                    new StringField('SMO_NAME')
                )
            );
            $lookupDataset->setOrderByField('SMO_NAME', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, 'multi_edit_REQUEST_FROM_SMO_search', 'SMO_ID', 'SMO_NAME', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $handler = new ImageHTTPHandler($this->dataset, 'RECEIVED_FILES', 'REQUEST_RECEIVED_FILES_handler_multi_edit', new NullFilter());
            GetApplication()->RegisterHTTPHandler($handler);
            new REQUEST_FROM_SMOModalViewPage($this, GetCurrentUserPermissionsForPage('REQUEST.FROM_SMO'));
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
            $cellFontColor['DUE_DATE'] = '#FFFFFF';
            $cellBoldAttr['DUE_DATE'] = true;
            $cellBgColor['DUE_DATE'] = '#008000';
                        
            $daysToSend = $rowData['DAYS_LEFT'];
            $is_send = $rowData['SEND'];
                        
                        
            if ($daysToSend < 3 && $is_send == 0)
               $cellBgColor['DUE_DATE'] = '#FF0000';
            elseif ($daysToSend < 20 && $is_send == 0){
               $cellFontColor['DUE_DATE'] = '#000000';
               $cellBgColor['DUE_DATE'] = '#FFFF00';
            }
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
            $docsDeadline = SMDateTime::now();
            $docsDeadline->addDays(10);
            $values['DUE_DATE'] = $docsDeadline;  
            $handled = true;
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
        protected function doAddEnvironmentVariables(Page $page, &$variables)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new REQUESTPage("REQUEST", "request.php", GetCurrentUserPermissionsForPage("REQUEST"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("REQUEST"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
