<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Filters\SalesRepresentativeFilter;
use App\Http\Requests\SRRequest;
use App\Models\Distributor;
use App\Models\SalesRepresentative;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;

class SalesRepresentativeController extends Controller
{
    public function index(Request $request, SalesRepresentativeFilter $filter)
    {
        $query = SalesRepresentative::with(['distributor', 'user']);
        if (Gate::allows('isDistributor')) {
            $distributorIds = Distributor::where('user_id',$request->user()->id)->pluck('id');
            $query = $query->whereIn('distributor_id',$distributorIds);
        }
        $salesRepresentatives = $query->filter($filter)->paginate();
        $input = $request->all();
        return view('sr.index', compact('salesRepresentatives', 'input'));
    }

    public function create()
    {
        return view('sr.create');
    }

    public function store(SRRequest $request)
    {
        $data = $request->only(['distributor_id', 'status', 'mobile']);

        try {
            DB::beginTransaction();
            $email = $request->input('mobile').'@deligram.com';

            if(!blank(User::where('email', $email)->first())) {
                throw ValidationException::withMessages(['mobile' => 'Phone number already taken.']);
            }

            $user = new User();
            $user->fill([
                'name' => $request->input('name'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'roles' => [Role::SALES_REPRESENTATIVE],
            ]);
            $user->save();

            $data['user_id'] = $user->id;
            if (Gate::allows('isDistributor')) {
                $distributorId = Distributor::where('user_id',$request->user()->id)->first()->id;
                $data['distributor_id'] = $distributorId;
            }

            $sr = new SalesRepresentative();
            $sr->fill($data);
            $sr->save();

            DB::commit();

            return redirect()->route('sr.index')->with(['_status' => 'success', '_msg' => 'Successfully Created Distributor!']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => $e->getMessage()]);
        }
    }

    public function edit(SalesRepresentative $sr)
    {
        $sr->load(['user', 'distributor']);

        return view('sr.edit', compact('sr'));
    }

    public function update(SalesRepresentative $sr, SRRequest $request)
    {
        $data = $request->only(['distributor_id', 'status', 'mobile']);

        if (Gate::allows('isDistributor')) {
            $distributorId = Distributor::where('user_id',$request->user()->id)->first()->id;
            $data['distributor_id'] = $distributorId;
        }

        try {
            DB::beginTransaction();

            $email = $request->input('mobile').'@deligram.com';

            if(!blank(User::where('email', $email)->where('id', '<>', $request->get('user_id'))->first())) {
                throw ValidationException::withMessages(['mobile' => 'Phone number already taken.']);
            }

            $user = [
                'name' => $request->get('name'),
                'email' => $email
            ];

            if(filled($request->get('password'))) {
                $user['password'] = bcrypt($request->get('password'));
            }

            $sr->user()->update($user);
            $sr->update($data);

            DB::commit();

            return redirect()->route('sr.index')->with(['_status' => 'success', '_msg' => 'Successfully Created SR!']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['_status' => 'fails', '_msg' => $e->getMessage()]);
        }
    }
}
