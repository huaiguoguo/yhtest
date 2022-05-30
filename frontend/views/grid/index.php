<?php
/**
 * @Author: masoner
 * @Date 2022/5/30 11:54
 * @CourseTitle xxxx
 * @var $provider
 * @var $supplierModel
 */

use yii\widgets\Pjax;

$this->title = "供应商列表";

$columns = [
    [
        'class' => 'yii\grid\CheckboxColumn',
        'name' => 'id',
        'headerOptions' => ['style' => 'text-align:center;font-size:15px;'],
        'header' => "<b title='全选' id='all-check' style='cursor: pointer'>全选</b>
                    <b title='反选' id='reverse-check' style='cursor: pointer'>反选</b>
                    <b title='清空' id='clear-check' style='cursor: pointer'>清空</b>",
        'checkboxOptions' => function ($model, $key, $index, $column) {
            return ['value' => $model->id, 'style' => 'cursor:pointer;'];
        },
        'contentOptions' => ['align' => 'center'],
    ],
    [
        'label' => 'ID',
        'attribute' => 'id',
        'options' => [
            'width' => '120px',
        ],
        'headerOptions' => [
            'style' => 'text-align:center;'
        ],
        'contentOptions' => [
            'align' => 'center'
        ],
    ],
    [
        'label' => '名称',
        'attribute' => 'name',
        'format' => 'raw',
        'headerOptions' => [
            'style' => 'text-align:center;'
        ],
        'contentOptions' => [
            'align' => 'center'
        ]
    ],
    [
        'label' => 'code',
        'attribute' => 'code',
        'headerOptions' => [
            'style' => 'text-align:center;'
        ],
        'contentOptions' => [
            'align' => 'center'
        ],
    ],
    [
        'label' => '状态',
        'attribute' => 't_status',
        'headerOptions' => [
            'style' => 'text-align:center;'
        ],
        'contentOptions' => [
            'align' => 'center'
        ],
        'filter' => ['ok' => 'ok', 'hold' => 'hold'],
        'filterInputOptions' => ['prompt' => '全部', 'class' => 'form-control']
    ]
];
Pjax::begin();
try {
    echo \yii\grid\GridView::widget([
        'id' => 'grid',
        'summary' => '展示 {begin}-{end}条, 共{totalCount}条 
                <div style="text-align: center; display: flex; flex-direction: row; justify-content: center; align-items: center;">
                    <div class="notice" style="display: none;padding: 0 10px;"></div>
                    <div class="selectedNumDiv" style="padding: 0 10px;">选择了<span id="selectedRowsNum">0</span>条</div>
                    <button class="btn btn-primary btn-sm" id="exportBtn" data-target="#exampleModal">导出</button>
                </div>
        ',
        'summaryOptions' => [
            'style' => 'display:flex; flex-direction: row; justify-content: space-between; padding-top:10px;'
        ],
        'caption' => '供应商管理',
        'captionOptions' => ['style' => 'font-size: 18px; font-weight: bold; color: #000; text-align: center; caption-side: top;'],
        "rowOptions" => function ($model, $key, $index, $grid) {
            return ['class' => $index % 2 == 0 ? 'label-red' : 'label-green'];
        },
        'dataProvider' => $provider,
        'filterModel' => $supplierModel,
        'pager' => [
            'firstPageLabel' => "首页",
            'prevPageLabel' => '<',
            'nextPageLabel' => '>',
            'lastPageLabel' => '尾页'
        ],
        'columns' => $columns,
        'emptyText' => '没有筛选到任何数据',
    ]);

} catch (Exception $e) {
    echo '错误消息:' . $e->getMessage() . PHP_EOL .
        '错误行数:' . $e->getLine();
}
?>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">请选中要导出的列</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="columns">
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="columns[]" id="id" value="id">
                        <label class="form-check-label" for="id">ID</label>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="columns[]" id="name" value="name">
                        <label class="form-check-label" for="name">名称</label>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="columns[]" id="code" value="code">
                        <label class="form-check-label" for="code">code</label>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="columns[]" id="t_status" value="t_status">
                        <label class="form-check-label" for="t_status">状态</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="exportSelected()">提交</button>
            </div>
        </div>
    </div>
</div>

<script>
    <?php $this->beginBlock('myselect');  ?>

    $(function () {
        let selectionAll = getSelectionAll();
        $('#grid').find("input[name='id[]']").each((index, item) => {
            let itemValue = parseInt($(item).prop("value"));
            if (selectionAll.includes(itemValue)) {
                $(item).prop("checked", true);
            }
        });
        $('#selectedRowsNum').text(selectionAll.length);
    })


    // 导出
    $("#exportBtn").on('click', () =>  {

        let selectedAllId = getSelectionAll();
        console.log(selectedAllId)
        if (selectedAllId.length<=0){
            let notice = $('.notice');
            notice.css({'font-size':'16px', 'color':'red'});
            notice.text('请选择要导出的记录')
            notice.fadeIn(500)

            $('.selectedNumDiv').css({'display': 'none'})
            return false;
        }else{
            $('.notice').fadeOut(500)
            $('.selectedNumDiv').css({'display': 'block'})
        }

        $('#exampleModal').modal();

    })

    //全选
    $('#all-check').on('click', () => {
        $('#grid').find("input[name='id[]']").each((index, item) => $(item).prop("checked", true));
        setSelection();

        $('.notice').css({'display':'none'})
        $('.selectedNumDiv').css({'display': 'block'})
    });

    //反选
    $('#reverse-check').on('click', () => {
        let selectedAllId = getSelectionAll();
        let newArr = [];
        $('#grid').find("input[name='id[]']").each(function (index, item) {
            let itemValue = parseInt($(item).prop('value'));
            if ($(item).prop('checked')) {
                $(item).prop("checked", false);
                if (selectedAllId.includes(itemValue)) {
                    selectedAllId.splice(selectedAllId.indexOf(itemValue), 1)
                }
            } else {
                newArr.push(itemValue);
                $(item).prop("checked", true);
            }
        });
        if (selectedAllId.length >= 0) {
            $('.notice').css({'display':'none'})
            $('.selectedNumDiv').css({'display': 'block'})

            if (newArr.length) {
                selectedAllId.push(...newArr);
                let newSelectedAllId = [...new Set(selectedAllId)];
                $('#selectedRowsNum').text(newSelectedAllId.length);
                sessionStorage.setItem('selected_rows', newSelectedAllId.join(','))
            } else {
                $('#selectedRowsNum').text(selectedAllId.length);
                sessionStorage.setItem('selected_rows', selectedAllId.join(','))
            }
        }

    });

    //清空
    $('#clear-check').on('click', () => {
        let selectedAllId = getSelectionAll();
        $('#grid').find("input[name='id[]']").each( (index, item) =>{
            $(item).prop("checked", false);
            let itemValue = $(item).prop('value');
            if (selectedAllId.includes(parseInt(itemValue))) {
                selectedAllId.splice(selectedAllId.indexOf(parseInt(itemValue)), 1)
            }
        });
        if (selectedAllId.length >= 0) {
            sessionStorage.setItem('selected_rows', selectedAllId.join(','))
        }
        $('#selectedRowsNum').text(selectedAllId.length);
    });


    function exportSelected() {

        let selectedIds = getSelectionAll();

        //检测是否选中导出列
        let columns = [];
        $("input[name='columns[]']").each((index,item)=>{
            if($(item).prop('checked')){
                let itemValue = $(item).prop('value');
                columns.push(itemValue);
            }
        })

        if (columns.length<=0){
            alert('请选择要导出的列');
            return false;
        }

        if (!columns.includes('id')){
            alert('ID是必选列');
            return false;
        }


        let data = {selectedIds: selectedIds.join(','), columns}
        $.ajax('/grid/export.html', {
            method: 'POST',
            data,
            success(res) {
                console.log(res);
                if (res.length >= 0) {
                    const data = JSON.parse(res)
                    const {filename, fileurl} = data;
                    if (fileurl) {
                        console.log(fileurl)
                        const a = document.createElement('a');
                        a.href = fileurl;
                        a.style.display = 'none';
                        a.setAttribute("download", filename)
                        a.click();
                        // document.body.appendChild(a)
                    }
                }
                $('#exampleModal').modal('hide');
            },
            error(err) {
                console.log(err);
            }
        })
    }


    function setSelection() {
        let currentSelectedId = $('#grid').yiiGridView('getSelectedRows');
        if (currentSelectedId.length <= 0) {
            return [];
        }

        const oldSelectedId = getSelectionAll();

        currentSelectedId.push(...oldSelectedId)

        let newSelectedId = [...new Set(currentSelectedId)]

        $('#selectedRowsNum').text(newSelectedId.length);

        sessionStorage.setItem('selected_rows', newSelectedId.join(','))
    }

    function getSelectionAll() {
        let oldSelected = sessionStorage.getItem('selected_rows');
        // console.log("oldSelectedStr===", oldSelected);
        if (!oldSelected) {
            return [];
        }
        let selectedIdParseInt = [];
        let oldSelectedArr = oldSelected.split(',');
        for (let item of oldSelectedArr) {
            selectedIdParseInt.push(parseInt(item))
        }
        // console.log("oldSelectedArr===", newArr);
        return selectedIdParseInt;
    }
    <?php $this->endBlock();  ?>

</script>



<?php
$this->registerJs($this->blocks['myselect'], \yii\web\View::POS_END);
$css = ".form-check{ display: flex; flex-direction: row; justify-content: space-between; align-items: center; }";
$this->registerCss($css);
Pjax::end();
?>



