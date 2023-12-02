<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\GoodOrServiceService;
use App\Services\InvoiceService;
use App\Services\RecipientService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    private CompanyService $companyService;
    private UserService $userService;
    private InvoiceService $invoiceService;
    private RecipientService $recipientService;
    private GoodOrServiceService $goodOrServiceService;

    public function __construct(CompanyService       $companyService, UserService $userService,
                                InvoiceService       $invoiceService, RecipientService $recipientService,
                                GoodOrServiceService $goodOrServiceService)
    {
        $this->companyService = $companyService;
        $this->userService = $userService;
        $this->invoiceService = $invoiceService;
        $this->recipientService = $recipientService;
        $this->goodOrServiceService = $goodOrServiceService;
    }

    public function index(): Factory|View|Application|RedirectResponse
    {
        $result = $this->companyService->findByUserId($this->userService->getUserIdWeb());
        $companies = $result['companies']->toArray();
        $invoice = [];
        $invoices = [];

        if (request()->has('invoice')) {
            $invoice = $this->invoiceService->show(request()->query('invoice'))['invoice'];
        }

        if (!request()->has('company')) {
            if (isset($this)) {
                $invoices = $this->invoiceService->findByCompanyId($companies[0]['id'])['invoices'];
            }
            return redirect()->route('invoices', ['company' => $companies[0]['id']])->
            with(['companies' => $companies, 'invoices' => $invoices, 'invoice' => $invoice]);
        } else {
            if (!empty($this->invoiceService->findByCompanyId(request()->query('company'))['invoices'])) {
                $invoices = $this->invoiceService->findByCompanyId(request()->query('company'))['invoices'];
            }
            return view('invoices', ['companies' => $companies, 'invoices' => $invoices, 'invoice' => $invoice]);
        }
    }

    public function createView(): Factory|View|Application
    {
        $issuer = [];
        $goods_and_services = [];
        $invoice_num = '';

        if (request()->has('company')) {
            if (!empty($this->companyService->show(request()->query('company'))['company'])) {
                $issuer = $this->companyService->show(request()->query('company'))['company'];
                $issuer['recipients'] = $this->recipientService->getByCompanyId(request()->query('company'))
                ['recipients']->toArray();
            }
            $goods_and_services = $this->goodOrServiceService->findByCompanyId(request()->query('company'))
            ['goods_or_services']->toArray();
            $invoices = $this->invoiceService->findByCompanyId(request()->query('company'))['invoices']->toArray();
            $invoice_num = count($invoices) + 1 . '/' . Carbon::now()->year;
        }

        return view('create_invoice', ['issuer' => $issuer, 'goods_and_services' => $goods_and_services,
            'invoice_num' => $invoice_num]);
    }

    public function create(Request $request): RedirectResponse
    {
        $requestArray = $request->except('_token');

        $requestArray = $this->castInvoiceValues($requestArray);

        $result = $this->invoiceService->store($requestArray, $requestArray['company_id']);

        if ($result['success']) {
            return redirect()->route('invoices', ['company' => $result['invoice']['company_id'],
                'invoice' => $result['invoice']['id']])->with(['message' =>
                $result['invoice']['invoice_num'] . ' ' . $result['message']]);
        } else {
            return back()->withErrors(['message' => $result['message']])->withInput(request()->all());
        }
    }

    /**
     * @param array $requestArray
     * @return array
     */
    public function castInvoiceValues(array $requestArray): array
    {
        $requestArray['fiscal_year'] = (int)$requestArray['fiscal_year'];

        foreach ($requestArray['invoice_items'] as $i => $invoice_item) {
            $requestArray['invoice_items'][$i]['unit_price'] = (float)$invoice_item['unit_price'];
            $requestArray['invoice_items'][$i]['quantity'] = (float)$invoice_item['quantity'];
            $requestArray['invoice_items'][$i]['rebate'] = (float)$invoice_item['rebate'];
            $requestArray['invoice_items'][$i]['vat_percentage'] = (float)$invoice_item['vat_percentage'];
        }

        return $requestArray;
    }
}
