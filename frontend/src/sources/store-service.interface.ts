import { ICacheableDataCollection } from '../interfaces/data-collection';
import { IDataResource, ICacheableDataResource } from '../interfaces/data-resource';
import { IObjectsById } from '../interfaces';

interface IStoreElement {
    time: number;
}

export interface IStoreService {
    getDataObject(type: 'collection', url: string): Promise<ICacheableDataCollection>;
    getDataObject(type: string, id: string): Promise<ICacheableDataResource>;
    getDataObject(type: 'collection' | string, id_or_url: string): Promise<ICacheableDataCollection | ICacheableDataResource>;
    getDataResources(keys: Array<string>): Promise<IObjectsById<ICacheableDataResource>>;
    saveResource(type: string, url_or_id: string, value: IDataResource): void;
    saveCollection(url_or_id: string, value: ICacheableDataCollection): void;
    clearCache();
    deprecateResource(type: string, id: string);
    deprecateCollection(key_start_with: string);
    removeObjectsWithKey(key: string);
}
