<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use Illuminate\Http\Request;
use App\Models\UserAddress;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        return view('user_addresses.index', ['addresses' => $request->user()->addresses]);
    }

    public function create()
    {
        return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);
    }

    // Use UserAddressRequest to validate form params
    public function store(UserAddress $userAddress, UserAddressRequest $request)
    {
        $this->authorize('own', $userAddress);

        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    public function edit(UserAddress $userAddress)
    {
        $this->authorize('own', $userAddress);

        return view('user_addresses.create_and_edit', ['address' => $userAddress]);
    }

    public function update(UserAddress $userAddress, UserAddressRequest $request)
    {
        $this->authorize('own', $userAddress);

        $userAddress->update($request->only(
            [
                'province',
                'city',
                'district',
                'address',
                'zip',
                'contact_name',
                'contact_phone',
            ]
        ));

        return redirect()->route('user_addresses.index');
    }

    public function destroy(UserAddress $userAddress)
    {
        $this->authorize('own', $userAddress);

        if ($userAddress->delete())
            return response()->json(['status' => '1', 'msg' => '删除成功']);
        else
            return response()->json(['status' => '0', 'msg' => '删除失败']);
    }
}
