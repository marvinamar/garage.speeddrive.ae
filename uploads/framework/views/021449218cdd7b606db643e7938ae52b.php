<?php global $s_v_data, $task, $user, $taskparts; ?>
                    <div class="modal-body">
                        <p>Task Details</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <h5><?=  $task->title ; ?></h5>

                                <?php if ($task->status == "Completed") { ?>
                                <span class="badge badge-sm badge-dot has-bg badge-success d-none d-mb-inline-flex">Completed</span>
                                <?php } else if ($task->status == "Cancelled") { ?>
                                <span class="badge badge-sm badge-dot has-bg badge-secondary d-none d-mb-inline-flex">Cancelled</span>
                                <?php } else { ?>
                                <span class="badge badge-sm badge-dot has-bg badge-warning d-none d-mb-inline-flex">In Progress</span>
                                <?php } ?>
                            </div>
                            <div class="col-sm-12">
                                <div class="">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="text-muted w-50">Cost:</td>
                                                <td class="fw-bold"><?=  money($task->cost, $user->parent->currency) ; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted w-50">Due Date:</td>
                                                <td class="fw-bold">
                                                    <?=  date("M j, Y", strtotime($task->due_date)) ; ?>
                                                    <?php if (!empty($task->due_time)) { ?>
                                                    • <?=  date("h:ia", strtotime($task->due_time)) ; ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <span class="text-muted">Task Description</span>
                                <div><?=  nl2br($task->description) ; ?></div>
                            </div>
                            <div class="col-sm-12">
                                <span class="text-muted">Required Parts</span>
                                <div>
                                    <ol>
                                        <?php if (!empty($taskparts)) { ?>
                                        <?php foreach ($taskparts as $key => $taskpart) { ?>
                                        <li><?=  $key + 1 ; ?>.) <?=  $taskpart->part->expense ; ?> • 
                                        <?php if ($taskpart->part->status == "Delivered") { ?>
                                        <span class="text-success">Delivered</span>
                                        <?php } else if ($taskpart->part->status == "Ordered") { ?>
                                        <span class="text-secondary">Ordered</span>
                                        <?php } else { ?>
                                        <span class="text-warning">Awaiting Delivery</span>
                                        <?php } ?>
                                        </li>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <li class="text-muted text-xs"><small><i>No parts required for this task.</i></small></li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                    </div>
                </form>
<?php return;
