import OrderProp from './OrderProp';
import React, { useContext } from "react";
import OrderContext from "./Context/OrderContext";

function OrderUserProps() {
    
    const { result } = useContext(OrderContext);
    const userFieldsGroup = 'Личные данные'

    const userGroup = result.ORDER_PROP.groups.find(group => group.NAME === userFieldsGroup)
    return (<div className="row">
        <div className="grid grid-cols-2 gap-x-2 bx-soa-customer p-0">
            {result.ORDER_PROP.properties.map(property => {

                if (property.PROPS_GROUP_ID !== userGroup.ID) {
                    return null
                }

                const disabled = false;
                return <OrderProp key={property.ID} property={property} disabled={disabled} />
            })}

        </div>
    </div>);
}

export default OrderUserProps;