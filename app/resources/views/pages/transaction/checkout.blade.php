@extends('layouts.admin')

@section('title', 'Checkout')

@section('js')
	<script src="{{ asset('js/datatable/transaction-checkout.js') }}" type="text/javascript"></script>
	<script>
		$('#checkout_print_inv').on('click','.btn-submitprint',function(){
			window.open("{{route('transaction.invoice',$transaction->id)}}", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes");
			$('#checkout_form').submit();
		})
		$('#checkout_print_inv').on('click','.btn-submit',function(){
			$('#checkout_form').submit();
		})
	</script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
	<form id="checkout_form" class="m-form m-form--label-align-right" method="post" action="{{ route('transaction.checkout', $transaction->id) }}">
		<div class="m-portlet__body">
			<div class="m-form__section m-form__section--first">
				<div class="form-group m-form__group row">
					<label class="col-lg-1 col-form-label">
						Invoice:
					</label>
					<div class="col-lg-4">
						<input type="text" class="form-control m-input m-input--solid" value="{{ $transaction->invoice }}" readonly>
					</div>
					<label class="col-lg-1 col-form-label">
						Note:
					</label>
					<div class="col-lg-6">
						<input type="text" class="form-control m-input m-input--solid" value="{{ $transaction->note }}" readonly>
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label class="col-lg-1 col-form-label">
						Date:
					</label>
					<div class="col-lg-4">
						<input type="text" class="form-control m-input m-input--solid" value="{{ explode(':', $transaction->date)[0] . ':' . explode(':', $transaction->date)[1] }}" readonly>
					</div>
					<label class="col-lg-1 col-form-label">
						Total:
					</label>
					<div class="col-lg-6">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">
								Rp
							</span>
							<input type="text" class="form-control m-input m-input--solid" value="{{ $total }}" readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="row align-items-center" style="margin-top: 40px">
				<div class="col-md-8">
					<div class="m-form__heading">
						<h3 class="m-form__heading-title">
							Products:
						</h3>
					</div>
				</div>
				<div class="col-md-4 "></div>
			</div>
			<!-- MAIN TABLE START -->
			<div class="m_datatable" id="m_datatable_checkout"></div>
		</div>
		<div class="m-portlet__foot m-portlet__foot--fit">
			<div class="m-form__actions m-form__actions">
				<div class="row">
					<div class="col-lg-5 m--align-right">
					</div>
					<div class="col-lg-6">
						{{ csrf_field() }}
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#checkout_print_inv">
							Checkout
						</button>
						<a href="{{ redirect()->back()->getTargetUrl() }}" type="reset" class="btn btn-secondary">
							Cancel
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div id="transaction-id" style="display: none;">{{ $transaction->id }}</div>
<div class="modal fade" id="checkout_print_inv" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirm Checkout?
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submitprint">
                        PRINT & CHECKOUT
					</button>
					<button type="button" class="btn btn-primary btn-submit">
						CHECKOUT NOW
					</button>
                </div>
            </div>
        </div>
    </div>
@endsection
