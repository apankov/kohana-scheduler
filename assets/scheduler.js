$(function () {
    $('tr.task').click(function (e) {
        var taskid = $(e.target).parent().attr('taskid');
        $('tr.task_details.task_' + taskid + ' td').toggle();
    });
});
